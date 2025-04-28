<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class PaymentController extends Controller
{
    public function index(Loan $loan)
    {
        return view('loan.loan-payments', [
            'loan' => $loan->load(['user', 'payments'])
        ]);
    }

    public function calculateNextDueDate(Loan $loan){
        $lastPaymentDate = $loan->payments->max('payment_date')
            ? Carbon::parse($loan->payments->max('payment_date'))
            : Carbon::parse($loan->created_at);

        return match ($loan->payment_plan) {
            'weekly' => $lastPaymentDate->copy()->addWeek(),
            'monthly' => $lastPaymentDate->copy()->addMonth(),
            default => $lastPaymentDate->copy()->addWeek(),
        };
    }

    public function repaymentAlerts()
    {
        // $unpaid_loans = Loan::unPaidLoans();
        // return view('loan.repayment-alerts', compact('unpaid_loans'));

        $dueSoonLoans = Loan::with(['user', 'payments'])
            ->where('status', 'active')
            ->get()
            ;

        return view('loan.repayment-alerts', compact('dueSoonLoans'));
    }

   



    public function sendRepaymentReminders(Request $request)
    {
        // Get validated recipients from request (if needed)
        $validated = $request->validate([
            'recipients' => 'sometimes|array', // Remove 'required' if not used
            'recipients.*' => 'exists:loans,id'
        ]);

        // Get unpaid loans (either all or filtered by request)
        $loans = isset($validated['recipients'])
            ? Loan::whereIn('id', $validated['recipients'])->unPaidLoans()
            : Loan::unPaidLoans();

        // Prepare SMS parameters from .env
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');
        $enqueue = 1;

        $failedRecipients = [];
        $successCount = 0;

        foreach ($loans as $loan) {
            try {
                // Skip loans without valid phone numbers
                if (!$loan->user->phone_number) {
                    Log::warning("SMS skipped - no phone number", ['loan_id' => $loan->id]);
                    continue;
                }

                // Calculate outstanding amount
                $outstandingAmount = number_format(
                    $loan->loan_required_amount - $loan->payments->sum('paid_amount')
                );

                // Build message
                $message = "Habari {$loan->user->formal_name}, "
                    . "Tunakukumbusha kwa deni la Tsh $outstandingAmount. "
                    . "Tafadhali endelea kulipia deni lako ili kuepuka usumbufu wowote.";

                // Send SMS
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'apiKey' => $apiKey,
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                            'username' => $username,
                            'to' => $this->formatPhoneNumber($loan->user->phone_number),
                            'from' => 'NK CNG',
                            'message' => $message,
                            'enqueue' => $enqueue,
                        ]);

                if ($response->successful()) {
                    $successCount++;
                    Log::info("SMS sent successfully", [
                        'phone' => $loan->user->phone_number,
                        'loan_id' => $loan->id
                    ]);
                } else {
                    $failedRecipients[] = [
                        'phone' => $loan->user->phone_number,
                        'error' => $response->json('SMSMessageData.Message') ?? 'Unknown error'
                    ];
                    Log::error("SMS failed", [
                        'phone' => $loan->user->phone_number,
                        'response' => $response->body()
                    ]);
                }

            } catch (\Exception $e) {
                $failedRecipients[] = [
                    'phone' => $loan->user->phone_number ?? 'N/A',
                    'error' => $e->getMessage()
                ];
                Log::critical("SMS exception", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return response()->json([
            'message' => "Reminders processed",
            'success_count' => $successCount,
            'failed_count' => count($failedRecipients),
            'failed_recipients' => $failedRecipients
        ], 200);
    }

    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
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
                'reference_number' => str::uuid()->toString(),
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


    public function filter(Request $request){

        $request->validate([
            'start_date'=> 'required|date',
            'end_date'=> 'required|date|after_or_equal:start_date',
        ]);

      $start_date=$request->start_date;
      $end_date=$request->end_date;

      $payment_report=Payment::whereDate('payment_date','>=',$start_date)
                               ->whereDate('payment_date','<=',$end_date)
                               ->get();
      return view('report.daily',compact('payment_report'));



    }


}
