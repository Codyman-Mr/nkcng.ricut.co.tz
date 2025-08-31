<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Payment;
use App\Models\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


$missedPayments = Loan::where('status', 'approved')
    ->get()
    ->filter(function ($loan) {
        return $loan->next_payment_date < Carbon::today(); // loan imelalila
    });

    

class AuthController extends Controller
{

    public $loan;

   public function mount()
{
   

    $installationId = Session::get('installation_id');

    if ($installationId) {
        $this->loan = Loan::where('installation_id', $installationId)->first();
    } else {
        $this->loan = null;
    }
}

    public function dashboard()
{
    $totalLoanAmount = Loan::sum('loan_required_amount');

    $allUsersCount = User::count();

    $customersWithLoansCount = DB::table('loans')
        ->where('status', 'approved')
        ->whereNotNull('applicant_name')
        ->distinct()
        ->count('applicant_name');

    $fullyPaidCustomersCount = DB::table('loans')
        ->join('payments', 'loans.id', '=', 'payments.loan_id')
        ->select('loans.installation_id', DB::raw('SUM(loans.loan_required_amount) as total_loan'), DB::raw('SUM(payments.paid_amount) as total_paid'))
        ->groupBy('loans.installation_id')
        ->havingRaw('SUM(payments.paid_amount) >= SUM(loans.loan_required_amount)')
        ->count();

    $payments = Payment::with('loan')->get();

    // Tunatumia $loans (plural) badala ya $loan (singular)
    $loans = Loan::with('payments')->get();

    $user = User::find(Auth::id());
    $users = User::all();

    $today = Carbon::now('Africa/Dar_es_Salaam')->startOfDay();
    $cutoffDate = $today->copy()->addDays(14);

    $nearEndLoans = Loan::query()
        ->whereNotNull('loan_end_date')
        ->whereBetween('loan_end_date', [$today, $cutoffDate])
        ->whereRaw('(SELECT COALESCE(SUM(paid_amount),0) FROM payments WHERE payments.loan_id = loans.id) < loans.loan_required_amount')
        ->orderBy('loan_end_date', 'asc')
        ->paginate(10);

    $paymentsThisWeek = Loan::query()->get()->filter(function ($loan) {
        $days = $loan->time_to_next_payment;
        return is_numeric($days) && $days >= 0 && $days <= 7;
    });

    $missedPayments = Loan::where('status', 'approved')
    ->get()
    ->filter(function ($loan) {
        return $loan->days_past_due !== null && $loan->days_past_due > 0 && $loan->days_past_due <= 7;
    });

    $locations = Location::with([
        'gpsDevice.customerVehicle'
    ])->get();

    return view('dashboard.index', compact(
        'payments',
        'user',
        'totalLoanAmount',
        'users',
        'nearEndLoans',
        'paymentsThisWeek',
        'missedPayments',
        'locations',
        'allUsersCount',
        'customersWithLoansCount',
        'fullyPaidCustomersCount',
        'loans' 
    ));
}

public function submitLoan(Request $request)
{
    $validated = $request->validate([
        'loan_package' => 'required|string',
        'cylinder_capacity' => 'required|string',
        'loan_required_amount' => 'required|numeric',
        'nida_number' => 'required|string|max:255',
        'loan_payment_plan' => 'required|string',
        'loan_start_date' => 'required|date',
        'loan_end_date' => 'required|date|after_or_equal:loan_start_date',
        'applicant_name' => 'required|string|max:255',
        'applicant_phone_number' => 'required|string|max:20',
        'initial_payment' => 'required|numeric|min:0',
    ]);

    // Hifadhi loan
    $loan = Loan::create([
        'user_id' => auth()->id() ?? 1,
        'installation_id' => null,
        'loan_package' => $validated['loan_package'],
        'cylinder_capacity' => $validated['cylinder_capacity'],
        'loan_required_amount' => $validated['loan_required_amount'],
        'loan_payment_plan' => $validated['loan_payment_plan'],
        'loan_start_date' => $validated['loan_start_date'],
        'loan_end_date' => $validated['loan_end_date'],
        'status' => 'approved',
        'nida_number' => $validated['nida_number'],
        'rejection_reason' => null,
        'applicant_name' => $validated['applicant_name'],
        'applicant_phone_number' => $validated['applicant_phone_number'],
        'reminders_sent' => 'not set',
        'remainder_log' => null,
    ]);

    // Hifadhi initial payment ukiongeza user_id
    Payment::create([
        'loan_id' => $loan->id,
        'user_id' => auth()->id() ?? 1,
        'paid_amount' => $validated['initial_payment'],
        'payment_date' => now(),
        'transaction_id' => 'CASH_' . $loan->id . '_' . \Illuminate\Support\Str::uuid(),
        'external_id' => 'CASH_' . $loan->id . '_' . \Illuminate\Support\Str::uuid(),
        'status' => 'pending',
        'job_status' => 'queued',
        'payment_method' => 'cash',
        'provider' => 'Cash',
        'receipt_path' => null,
    ]);

    // --- SMS SENDING LOGIC ---
    $phoneNumber = $loan->applicant_phone_number;
    $fullName = $loan->applicant_name;

    // Format number to +255 if it starts with 0 and is 10 digits
    if (preg_match('/^0\d{9}$/', $phoneNumber)) {
        $phoneNumber = '+255' . substr($phoneNumber, 1);
    }

    $username = 'MIKE001';
    $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
    $enqueue = 1;

   $message = "Congratulations $fullName! Your loan for '{$loan->loan_package}' amounting to TZS " . number_format($loan->loan_required_amount, 2) . " has been approved. You may start using it now. if there is any case contact us 0655414857 Thank you.";

    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => $apiKey,
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to' => $phoneNumber,
            'from' => 'NK CNG',
            'message' => $message,
            'enqueue' => $enqueue,
        ]);

        if ($response->successful()) {
            Log::info('SMS sent successfully to customer', [
                'phone' => $phoneNumber,
                'message' => $message,
            ]);
        } else {
            Log::error('Failed to send SMS', [
                'response' => $response->body(),
                'phone' => $phoneNumber,
            ]);
        }
    } catch (\Exception $e) {
        Log::error('SMS sending exception', [
            'error' => $e->getMessage(),
            'phone' => $phoneNumber,
        ]);
    }
    // --- END SMS SENDING LOGIC ---

    return redirect()->back()->with('success', 'Loan application submitted successfully! SMS sent to customer.');
}


    public function showLoanForm()
{
    return view('loan.application'); // Au jina la blade yako ya form
}



    public function registrationPage()
    {
        return view('auth.registration');
    }

   
    public function registration(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => [
                'required',
                'unique:users,phone_number',
                'regex:/^(?:\+2557\d{8}|\+2556\d{8}|06\d{8}|07\d{8})$/'
            ],
            'password' => 'required|confirmed|min:5',
        ], [
            'phone_number.unique' => "The given phone number is already in use",
            'password.confirmed' => '"Password" and "Confirm Password" should match',
            'phone_number.regex' => 'Invalid phone number',
        ]);

        $enqueue = 1;
        $username = 'MIKE001';
        $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
        $verification_code = rand(1111, 9999);

        $phoneNumber = $request->phone_number;

        if (preg_match('/^06\d{8}|07\d{8}$/', $phoneNumber)) {
            $phoneNumber = '+255' . substr($phoneNumber, 1);
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => $apiKey,
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to' => $phoneNumber,
            'from' => 'NK CNG',
            'message' => "Your verification code is {$verification_code}",
            'enqueue' => $enqueue,
        ]);

        if ($response->successful()) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'verification_code' => $verification_code
            ]);

            Session::put('user', $user);
            return redirect('/otp-verification');
        } else {
            return back()->withErrors(['other_errors' => 'Failed to send Verification Code']);
        }
    }

    public function verificationPage(Request $request)
    {
        $user = $request->session()->get('user');

        if ($user) {
            return view('auth.verification', compact('user'));
        } else {
            return back()->withErrors(['other_errors' => 'OTP Verification failed.']);
        }
    }

    public function verifyOtp(Request $request, User $user)
    {
        $request->validate([
            'otp' => 'required|array|size:4',
            'otp.*' => 'required|string|size:1'
        ]);

        $otp = intval(implode('', $request->input('otp')));
        $correctOtp = $user->verification_code;

        if ($otp !== $correctOtp) {
            return back()->withErrors(['verification_error' => 'Incorrect verification code.']);
        }

        $user->update([
            'status' => 'verified'
        ]);

        Auth::login($user);
        Session::forget('user_id');

        return redirect('/welcome-page')->with('message', 'User Verified and Logged In');
    }

    public function welcomePage()
    {
        return view('welcome');
    }

    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required',
        ], [
            'phone_number.required' => "Phone Number is required",
            'password.required' => "Password is required"
        ]);

        $credentials = $request->only('phone_number', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->banned) {
                return redirect()->intended('/');
            } else {
                Auth::logout();
                return back()->withErrors(['password' => 'Your account has been banned.'])->onlyInput('phone_number');
            }
        }

        return back()->withErrors(['password' => 'Invalid Credentials'])->onlyInput('phone_number', 'password');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back();
    }
}
