<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function dashboard()
    {
        return view('dashboard.index', [
            'payments' => Payment::with('loan')->get(),
            'user' => Auth::user()->load('loans')
        ]);
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

        auth()->login($user);
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
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back();
    }
}