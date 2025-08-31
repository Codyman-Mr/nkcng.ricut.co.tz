<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\CylinderType;
use App\Models\Installation;
use App\Models\LoanDocument;
use App\Models\User;
use App\Models\Payment;

use Illuminate\Http\Request;
use App\Models\CustomerVehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{


    public function create(User $user = null)
    {
        $borrower = $user != null ? $user : Auth::user();
        return view('loan.application', compact('borrower'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'dob' => ['required', 'date', 'before:' . now()->subYears(18)->format('Y-m-d')],
            'gender' => 'required',
            'nida_no' => 'required|string|max:255',
            'nida_front_view' => 'file|mimes:jpeg,jpg,png,pdf',
            'address' => 'required|string|max:255',
            'gvt_identification_letter' => 'required|file|mimes:jpeg,jpg,png,pdf',
            'vehicle_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'vehicle_registration_card' => 'required|file|mimes:jpeg,jpg,png,pdf',
            'plate_number' => 'required|string|max:255',
            'fuel_type' => 'required|string|max:255',
            'gvt_guarantor_first_name' => 'required|string|max:255',
            'gvt_guarantor_last_name' => 'required|string|max:255',
            'gvt_guarantor_phone_number' => 'required|string|max:255',
            'gvt_guarantor_nida_no' => 'required|string|max:255',
            'gvt_guarantor_nida_front_view' => 'file|mimes:jpeg,jpg,png,pdf',
            'gvt_guarantor_letter' => 'required|file|mimes:jpeg,jpg,png,pdf',
            'private_guarantor_first_name' => 'required|string|max:255',
            'private_guarantor_last_name' => 'required|string|max:255',
            'private_guarantor_phone_number' => 'required|string|max:255',
            'private_guarantor_nida_no' => 'required|string|max:255',
            'private_guarantor_nida_front_view' => 'file|mimes:jpeg,jpg,png,pdf',
            'private_guarantor_letter' => 'required|file|mimes:jpeg,jpg,png,pdf',
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'dob' => $request->dob,
                'gender' => $request->gender,
                'nida_number' => $request->nida_no,
                'address' => $request->address,
            ]);

            $vehicle = CustomerVehicle::create([
                'user_id' => Auth::id(),
                'model' => $request->vehicle_name,
                'plate_number' => $request->plate_number,
                'vehicle_type' => $request->vehicle_type,
                'fuel_type' => $request->fuel_type,
            ]);

            $installation = Installation::create([
                'customer_vehicle_id' => $vehicle->id,
                'cylinder_type_id' => '1',
                'status' => 'pending',
                'payment_type' => 'loan',
            ]);
            if (Loan::where('user_id', $user->id)->where('status', 'approved')->exists()) {
                return response()->json(['message' => 'User already has an approved loan.'], 400);
            }

  
$loan = Loan::create([
    'user_id' => $user->id,
    'installation_id' => $installation->id,
    'loan_required_amount' => str_replace(',', '', $request->loan_required_amount),
    'applicant_name' => $request->first_name . ' ' . $request->last_name,
    
]);


            // \Log::info('New loan created', [
            //     'loan_id' => $loan->id,
            //     'loan_required_amount' => $loan->loan_required_amount,
            //     'user_id' => $user->id,
            // ]);

            $existingLoan = Loan::where('user_id', $user->id)->where('status', 'approved')->first();

            if ($existingLoan) {
                // \Log::info('Loan creation aborted: User already has an approved loan.', [
                //     'user_id' => $user->id,
                //     'existing_loan_amount' => $existingLoan->loan_required_amount,
                // ]);
                return response()->json(['message' => 'User already has an approved loan.'], 400);
            }


            $this->saveLoanDocument($loan->id, "{$user->first_name} {$user->last_name} - Vehicle Registration Card", $request->file('vehicle_registration_card'));

            if ($request->file('nida_front_view')) {
                $this->saveLoanDocument($loan->id, "{$user->first_name} {$user->last_name} - ID Front View", $request->file('nida_front_view'));
            }
            $this->saveLoanDocument($loan->id, 'Government Identification Letter', $request->file('gvt_identification_letter'));

            if ($request->file('gvt_guarantor_nida_front_view')) {
                $this->saveLoanDocument($loan->id, "{$request->gvt_guarantor_first_name} {$request->gvt_guarantor_last_name} - ID Front View: {$request->gvt_guarantor_nida_no}", $request->file('gvt_guarantor_nida_front_view'));
            }
            $this->saveLoanDocument($loan->id, "Support Letter from {$request->gvt_guarantor_first_name} {$request->gvt_guarantor_last_name}, Local Government Guarantor", $request->file('gvt_guarantor_letter'));

            if ($request->file('private_guarantor_nida_front_view')) {
                $this->saveLoanDocument($loan->id, "{$request->private_guarantor_first_name} {$request->private_guarantor_last_name} - ID Front View: {$request->private_guarantor_nida_no}", $request->file('private_guarantor_nida_front_view'));
            }
            $this->saveLoanDocument($loan->id, "Support Letter from {$request->private_guarantor_first_name} {$request->private_guarantor_last_name}, Guarantor With Permanent Contract", $request->file('private_guarantor_letter'));

            DB::commit();

            return response()->json(['message' => 'Loan application submitted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function saveLoanDocument($loanId, $documentType, $file)
    {
        $filePath = $file->store('loan_documents', 'public');

        LoanDocument::create([
            'loan_id' => $loanId,
            'document_type' => $documentType,
            'document_path' => $filePath,
        ]);
    }



    public function show(Loan $loan)
    {
        return view(view: 'loan.show-loan', data: [
            'loan' => $loan->load(['user', 'documents', 'installation.customerVehicle']),
            'required_amount' => $loan->loan_required_amount,
            'loan_payment_plan' => $loan->loan_payment_plan,
            'cylinders' => CylinderType::get()
        ]);
    }

    public function Loan_sum(Loan $loan)
    {
        // Get loans for the dashboard
        $loans = Loan::where('status', 'approved')->get();

        // Log loan amounts for debugging
        // \Log::info('Loan amounts being summed:', $loans->pluck('loan_required_amount')->toArray());

        // Sum up the amounts
        $loan_add = $loans->sum('loan_required_amount');
        // \Log::info('Total loan amount:', ['total' => $loan_add]);

        return view('dashboard.index', compact('loan_add'));
    }

    public function pendingLoans()
    {
        return view('loan.pending-loans', [
            'loans' => Loan::where('status', 'pending')->with(['installation.customerVehicle.user'])->paginate(5)->onEachSide(2),
        ]);
    }

    public function ongoingLoans(Request $request)
    {
        // Get all payments with their associated loans
        $payments = Payment::with('loan')->get();

        // Get the current logged-in user and load their loans
        $user = Auth::user();


        // Calculate total loan amount for all users
        $totalLoanAmount = Loan::sum('loan_required_amount');

        // Fetch all users with their loans and payments
        $users = User::with('loans.payments')->get();

        // Fetch loans that are near their end date
        $nearEndLoans = Loan::whereNotNull('loan_end_date') // Ensure the end date exists
            ->whereDate('loan_end_date', '>', now()) // Filter loans with end date in the future
            ->orderBy('loan_end_date', 'asc') // Sort by nearest end date
            ->take(12) // Limit to 12 records
            ->with('user') // Include the user associated with the loan
            ->get();

        $loans = Loan::whereHas('installation', function ($query) {
    $query->where('status', 'complete');
})
->with(['installation.customerVehicle'])
->paginate(10)
->onEachSide(2);

        // Get the search query from the request
        $query = $request->input('query');

        // Performing the search
        $loans_query = Loan::whereHas('user', function ($q) use ($query) {
            $q->where('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%");
        })
            ->orWhere('loan_required_amount', 'like', "%$query%")
            ->orWhere('loan_end_date', 'like', "%$query%")
            ->get();

        return view('loan.ongoing', compact('payments', 'user', 'totalLoanAmount', 'users', 'nearEndLoans', 'loans', 'loans_query'));
    }



    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return redirect()->route(''); // Redirect back if query is empty
        }

        $loans = Loan::whereHas('user', function ($q) use ($query) {
            $q->where('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%");
        })
            ->orWhere('loan_required_amount', 'like', "%$query%")
            ->orWhere('loan_end_date', 'like', "%$query%")
            ->get();

        // Pass the filtered loans to the view
        return view('loan.ongoing', compact('loans'));
    }

    public function approveLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'cylinder_type' => 'required|exists:cylinder_types,id',
            'loan_required_amount' => 'required|string',
            'loan_payment_plan' => 'required|string',
        ]);



        try {
            $loan->update([
                'loan_required_amount' => str_replace(',', '', $request->loan_required_amount),
                'loan_payment_plan' => $request->loan_payment_plan,
                'status' => 'approved',
            ]);

            $loan->installation->update([
                'cylinder_type_id' => $request->cylinder_type,
            ]);

            return response()->json(['message' => "Loan approved successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
