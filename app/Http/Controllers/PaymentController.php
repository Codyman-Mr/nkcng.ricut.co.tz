<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function index(Loan $loan)
    {
        return view('loan.loan-payments', [
            'loan' => $loan->load(['user', 'payments'])
        ]);
    }

    public function repaymentAlerts()
    {
        $unpaid_loans = Loan::unPaidLoans();
        return view('loan.repayment-alerts', compact('unpaid_loans'));
    }

    public function sendRepaymentReminders(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
        ]);

        $unpaid_loans = Loan::unPaidLoans();
        $recipients = [];

        foreach ($unpaid_loans as $key => $loan) {
            $recipients[] = [
                'message' => "Habari " . Str::title($loan->user->first_name) . " " . Str::title($loan->user->last_name) . ", Tunakuandikia kukukumbusha kwamba bado unadaiwa kiasi cha " . number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) . ". Tafadhali endelea kulipia deni lako ili kuepuka usumbufu wowote.",
                'phoneNumber' => $this->convertPhoneNumberToInternationalFormat($loan->user->phone_number),
            ];
        }

        $enqueue = 1;
        $username = 'MIKE001';
        $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';

        try {
            foreach ($recipients as $recipient) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'apiKey' => $apiKey,
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to' => $recipient['phoneNumber'],
                    'from' => 'NK CNG',
                    'message' => $recipient['message'],
                    'enqueue' => $enqueue,
                ]);

                if (!$response->successful()) {
                    return response()->json(['message' => 'Failed to send SMS to ' . $recipient['phoneNumber'], 'error' => $response->body()], 500);
                }
            }

            return response()->json(['message' => 'SMS sent successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while sending SMS', 'error' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'paid_amount' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        try {
            Payment::create([
                'loan_id' => $loan->id,
                'payment_date' => $request->payment_date,
                'paid_amount' => str_replace(',', '', $request->paid_amount),
                'payment_method' => $request->payment_method,
            ]);

            return response()->json(['message' => "Payment received successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function generatePaymentToken()
    {
        try {
            $url = 'https://fescoin.tappesa.com/v3/payment/get-token';
            $payload = [
                'client_id' => 'ac8537dba9f7eab9dd2f02361e937035',
                'client_secret' => 'e43e9cf1ab8d0934c0c26f713f09a43e',
                'grant_type' => 'client_credentials'
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->failed()) {
                throw new \Exception('Failed to retrieve payment token');
            }

            $tokenDetails = $response->json();

            return $tokenDetails;
        } catch (\Exception $e) {
            return ['access_token' => null, 'error' => $e->getMessage()];
        }
    }

    public function loanPayment(Request $request)
    {
        try {
            $token = $this->generatePaymentToken();

            if (isset($token['error'])) {
                throw new \Exception($token['error']);
            }

            $accessToken = $token['access_token'];

            $paymentData = [
                'reference_number' => str_shuffle(Str::random(15) . now()->timestamp),
                'payment_network' => 30,
                'buyer_phone_number' => $request->input('payer_phone_number'),
                'reason' => $request->input('reason'),
                'buyer_name' => 'Vince Richard',
                'ipn_url' => 'https://nkcng.free.beeceptor.com',
                'passway' => 20,
                'payList' => [
                    [
                        'payment_network' => 10,
                        'phone_number' => '+255768591818',
                        'amount' => 100
                    ]
                ]
            ];


            $url = 'https://fescoin.tappesa.com/v3/payment/make-payment';
            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ])->post($url, $paymentData);

            if ($response->failed()) {
                throw new \Exception('Failed to make payment');
            }

            return response()->json($response->json(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();
            return response()->json(['message' => "Payment deleted successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
   
    public function report_payment()
    { 
        $payment_report = Payment::all();
        return view('report.daily', compact('payment_report'));
    }
    public function filter(Request $request){
      $start_date=$request->start_date;
      $end_date=$request->end_date;

      $payment_report=Payment::whereDate('payment_date','>=',$start_date)
                               ->whereDate('payment_date','<=',$end_date)
                               ->get();
      return view('report.daily',compact('payment_report'));

                         

    }
     
    
}