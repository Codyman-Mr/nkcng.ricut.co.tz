<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\CylinderType;
use App\Models\Installation;
use App\Models\LoanDocument;
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
            'dob' => 'required|date',
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
                'user_id' => auth()->id(),
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

            $loan = Loan::create([
                'user_id' => $user->id,
                'installation_id' => $installation->id,
            ]);

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
        return view('loan.show-loan', [
            'loan' => $loan->load(['user', 'documents', 'installation.customerVehicle']),
            'cylinders' => CylinderType::get()
        ]);
    }

    public function pendingLoans()
    {
        return view('loan.pending-loans', [
            'loans' => Loan::where('status', 'pending')->with(['installation.customerVehicle.user'])->get()
        ]);
    }

    public function ongoingLoans()
    {
        return view('loan.ongoing', [
            'loans' => Loan::where('status', 'approved')->with(['installation.customerVehicle'])->get()
        ]);
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