<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Models\Payment;
use App\Events\PaymentStatusUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InitiatePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $loanId;
    protected $amount;
    protected $phoneNumber;
    protected $provider;

    public function __construct($loanId, $amount, $phoneNumber, $provider)
    {
        $this->loanId = $loanId;
        $this->amount = $amount;
        $this->phoneNumber = $phoneNumber;
        $this->provider = $provider;
    }

    protected function getBearerToken()
    {
        $credentials = [
            'appName' => 'nkcng',
            'clientId' => '28acd4c4-5011-401c-ac35-17b21c98ccaf',
            'clientSecret' => 'ByeqcxZIHX72d3Gfz1RwnJ4+eXTeXmvPjuhiKc4eRn88KCODQKL+byTeB+H9ZAa1UyMGo2LNKApfMlopg687Y+higp4P1vyLoCpVHScuQaI2MsoVp0BoeDm+6IPyb6Rp81Auns3nhp2kf17WFLrDVT9HpdmXxyHAezLbfNM5au5c4yPVyEkAzIENSGsvW5YbT0N0Uak3UGeUq8T7vuEmon80B+JT/dmhYBUyWtDHpZApAnRRCbUW+b+40QpnWlAJkhF8mVPwrRqgQh7gMPdA2kmVKUIoI6oqIMXnBEXpWcDmMWpYsBMfHigXZk+rm0qOCJf26WfaJm0ooSBRqq9XYAvM/UfHptL1ncGtUoRhzkxgETFhUfUIke0btoG6mtB6OpPh/Fdh//NKGRNqh8DCKCsCs8LFdHEz82uX0CQxb6Js+RGdsPCSw87R0TrPvv1KLmWMNRnFZ1+PRF44wV8AgGdq5NjByhUb7pg3gsSHAGR9XSZ9auFKq52mEb8Yhnd0Gnv5iLNc2sCj46yDQIN0EBr9ze/10/JyZW5Bep/1/nYX8+DiqMGz4sCyt5f+vJMz1oFsw4J2CmbPhFwRVrjV3nRjTg0jPsECbyIE73uEjCQPP0BR/XsVrX2/BV0Asx+Xt9Fd0qaOyeSSFWvXj1EyVbQ7tbfbLSrxMOk8oVLy0hM=',
        ];

        Log::info('Requesting Bearer token', [
            'loan_id' => $this->loanId,
            'endpoint' => 'https://authenticator-sandbox.azampay.co.tz/AppRegistration/GenerateToken',
        ]);

        $response = Http::post('https://authenticator-sandbox.azampay.co.tz/AppRegistration/GenerateToken', $credentials);

        if (!$response->successful()) {
            throw new \Exception('Failed to generate Bearer token: ' . $response->body());
        }

        $token = $response->json('data.accessToken') ?? null;
        if (!$token) {
            throw new \Exception('No access token returned in response: ' . $response->body());
        }

        Log::info('Bearer token generated successfully', ['loan_id' => $this->loanId]);

        return $token;
    }

    public function handle()
    {
        try {
            $loan = Loan::findOrFail($this->loanId);
            $userId = $loan->user_id;

            if (!$userId) {
                throw new \Exception('No user_id associated with loan ID: ' . $this->loanId);
            }

            Log::info('Initiating payment job', [
                'loan_id' => $this->loanId,
                'user_id' => $userId,
                'amount' => $this->amount,
                'phone_number' => $this->phoneNumber,
                'provider' => $this->provider,
            ]);

            // Get Bearer token
            $token = $this->getBearerToken();

            // Make payment API call with Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->post('https://sandbox.azampay.co.tz/azampay/mno/checkout', [
                        'accountNumber' => $this->phoneNumber,
                        'amount' => $this->amount,
                        'currency' => 'TZS',
                        'externalId' => 'TXN_' . uniqid(),
                        'provider' => $this->provider,
                    ]);

            $paymentData = [
                'loan_id' => $this->loanId,
                'user_id' => $userId,
                'paid_amount' => $this->amount,
                'payment_date' => now()->toDateString(),
                'transaction_id' => $response->successful() ? ($response->json('transactionId') ?? 'TXN_' . uniqid()) : 'TXN_FAILED_' . uniqid(),
                'external_id' => $response->successful() ? ($response->json('externalId') ?? 'EXT_' . uniqid()) : 'EXT_FAILED_' . uniqid(),
                'status' => $response->successful() ? 'pending' : 'failed',
                'job_status' => $response->successful() ? 'completed' : 'failed',
                'payment_method' => 'mobile_money',
                'provider' => $this->provider,
                'phone_number' => $this->phoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('Creating payment record', [
                'loan_id' => $this->loanId,
                'payment_data' => $paymentData,
            ]);

            $payment = Payment::create($paymentData);

            $message = $response->successful()
                ? "Payment of TZS {$this->amount} for Loan #{$this->loanId} initiated successfully. Please complete the transaction on your phone."
                : "Payment of TZS {$this->amount} for Loan #{$this->loanId} failed. Please try again or contact support.";

            event(new PaymentStatusUpdated($userId, $this->loanId, $message, $payment->job_status));

            Log::info('Payment job processed', [
                'loan_id' => $this->loanId,
                'payment_id' => $payment->id,
                'status' => $payment->job_status,
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            $paymentData = [
                'loan_id' => $this->loanId,
                'user_id' => $userId ?? null,
                'paid_amount' => $this->amount,
                'payment_date' => now()->toDateString(),
                'transaction_id' => 'TXN_ERROR_' . uniqid(),
                'external_id' => 'EXT_ERROR_' . uniqid(),
                'status' => 'failed',
                'job_status' => 'failed',
                'payment_method' => 'mobile_money',
                'provider' => $this->provider,
                'phone_number' => $this->phoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('Creating failed payment record', [
                'loan_id' => $this->loanId,
                'payment_data' => $paymentData,
            ]);

            $payment = Payment::create($paymentData);

            $message = "Payment of TZS {$this->amount} for Loan #{$this->loanId} failed: {$e->getMessage()}";

            event(new PaymentStatusUpdated($userId ?? null, $this->loanId, $message, 'failed'));

            Log::error('Payment job failed: ' . $e->getMessage(), [
                'loan_id' => $this->loanId,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
