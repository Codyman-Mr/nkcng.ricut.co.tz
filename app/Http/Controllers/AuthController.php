<?php

namespace App\Http\Controllers;
use App\Models\Loan;
use App\Models\User;
use App\Models\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Location;

class AuthController extends Controller
{

    public $loan;

    public function mount()
    {
        // Fetch the loan for the logged-in user
        $this->loan = Loan::where('user_id', Auth::id())->first();
    }

    public function dashboard()
    {
        // Get all payments with their associated loans
        $payments = Payment::with('loan')->get();

        // Get the current logged-in user and load their loans
        $user = User::with('loans')->find(Auth::id());

        // Calculate total loan amount for all users
        $totalLoanAmount = Loan::sum('loan_required_amount');

        // Fetch all users with their loans and payments
        $users = User::with('loans.payments')->get();

        $loan = $this->loan;


        $nearEndLoans = Loan::with('user')
            ->whereNotNull('loan_end_date')
            ->whereDate('loan_end_date', '>', now())
            ->where(function ($query) {
                $query->whereRaw('(
            SELECT COALESCE(SUM(paid_amount), 0)
            FROM payments
            WHERE payments.loan_id = loans.id
        ) < loans.loan_required_amount');
            })
            ->orderBy('loan_end_date', 'asc')
            ->paginate(10);





        // Payments due within 7 days (based on time_to_next_payment attribute)
        $paymentsThisWeek = Loan::with('user')
            ->get()
            ->filter(function ($loan) {
                $days = $loan->time_to_next_payment;
                return is_numeric($days) && $days >= 0 && $days <= 7;
            });

        // Missed payments (time_to_next_payment is negative)
        $missedPayments = Loan::with('user')
            ->get()
            ->filter(function ($loan) {
                $days = $loan->time_to_next_payment;
                return is_numeric($days) && $days < 0;
            });

        $locations = Location::with([
            'gpsDevice.user.loans',
            'gpsDevice.customerVehicle'
        ])->get();


        return view('dashboard.index', compact(
            'payments',
            'user',
            'totalLoanAmount',
            'users',
            'loan',
            'nearEndLoans',
            'paymentsThisWeek',
            'missedPayments',
            'locations'
        ));
    }



    public function registrationPage()
    {
        return view('auth.registration');
    }

    // original registration function
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



// // namespace App\Http\Controllers;
