<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function getAccessToken()
    {
        try {
            $response = Http::post("https://authenticator-sandbox.azampay.co.tz/AppRegistration/GenerateToken", [
                'appName' => env('AZAMPAY_APP_NAME', 'nkcng'),
                'clientId' => env('AZAMPAY_CLIENT_ID', '28acd4c4-5011-401c-ac35-17b21c98ccaf'),
                'clientSecret' => env('AZAMPAY_CLIENT_SECRET', 'ByeqcxZIHX72d3Gfz1RwnJ4+eXTeXmvPjuhiKc4eRn88KCODQKL+byTeB+H9ZAa1UyMGo2LNKApfMlopg687Y+higp4P1vyLoCpVHScuQaI2MsoVp0BoeDm+6IPyb6Rp81Auns3nhp2kf17WFLrDVT9HpdmXxyHAezLbfNM5au5c4yPVyEkAzIENSGsvW5YbT0N0Uak3UGeUq8T7vuEmon80B+JT/dmhYBUyWtDHpZApAnRRCbUW+b+40QpnWlAJkhF8mVPwrRqgQh7gMPdA2kmVKUIoI6oqIMXnBEXpWcDmMWpYsBMfHigXZk+rm0qOCJf26WfaJm0ooSBRqq9XYAvM/UfHptL1ncGtUoRhzkxgETFhUfUIke0btoG6mtB6OpPh/Fdh//NKGRNqh8DCKCsCs8LFdHEz82uX0CQxb6Js+RGdsPCSw87R0TrPvv1KLmWMNRnFZ1+PRF44wV8AgGdq5NjByhUb7pg3gsSHAGR9XSZ9auFKq52mEb8Yhnd0Gnv5iLNc2sCj46yDQIN0EBr9ze/10/JyZW5Bep/1/nYX8+DiqMGz4sCyt5f+vJMz1oFsw4J2CmbPhFwRVrjV3nRjTg0jPsECbyIE73uEjCQPP0BR/XsVrX2/BV0Asx+Xt9Fd0qaOyeSSFWvXj1EyVbQ7tbfbLSrxMOk8oVLy0hM='),
            ]);

            $data = $response->json();
            Log::info('Token generation response', ['response' => $data]);

            if ($response->successful() && isset($data['data']['accessToken'])) {
                Log::info('Access token generated successfully', ['token' => substr($data['data']['accessToken'], 0, 20) . '...']);
                return $data['data']['accessToken'];
            }

            Log::error('Failed to generate access token', ['response' => $data]);
            throw new \Exception('Failed to generate access token: ' . ($data['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Token generation error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function initiatePayment($loanId, $amount, $phoneNumber, $provider)
    {
        $loan = \App\Models\Loan::findOrFail($loanId);
        $externalId = 'LOAN_' . $loan->id . '_' . time();

        // Normalize phone number to +255 format
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+255' . substr($phoneNumber, 1);
        } elseif (substr($phoneNumber, 0, 4) !== '+255') {
            $phoneNumber = '+255' . $phoneNumber;
        }

        $payload = [
            'appName' => env('AZAMPAY_APP_NAME', 'nkcng'),
            'clientId' => env('AZAMPAY_CLIENT_ID', '28acd4c4-5011-401c-ac35-17b21c98ccaf'),
            'currency' => 'TZS',
            'externalId' => $externalId,
            'amount' => $amount,
            'provider' => $provider,
            'accountNumber' => $phoneNumber,
            'redirectUrls' => [
                'successRedirectUrl' => url('/success'),
                'failureRedirectUrl' => url('/failure')
            ]
        ];

        try {
            $token = $this->getAccessToken();
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ];

            Log::info('Initiating payment', ['payload' => $payload, 'headers' => ['Authorization' => 'Bearer ' . substr($token, 0, 20) . '...']]);

            $response = Http::retry(10, 2000)->withHeaders($headers)
                ->post('https://sandbox.azampay.co.tz/azampay/mno/checkout', $payload);

            $responseData = $response->json();
            Log::info('AzamPay mobile checkout response', ['response' => $responseData]);

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] && isset($responseData['transactionId'])) {
                return $this->processPayment($loan, $amount, $phoneNumber, $provider, $externalId, $responseData);
            }

            Log::error('Payment initiation failed', ['response' => $responseData]);
            return ['success' => false, 'message' => 'Failed to initiate payment: ' . ($responseData['message'] ?? 'Unknown error')];
        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage(), ['loan_id' => $loanId, 'trace' => $e->getTraceAsString()]);
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    protected function processPayment($loan, $amount, $phoneNumber, $provider, $externalId, $responseData)
    {
        $payment = Payment::create([
            'loan_id' => $loan->id,
            'user_id' => $loan->user_id,
            'paid_amount' => $amount,
            'transaction_id' => $responseData['transactionId'],
            'external_id' => $externalId,
            'status' => 'pending',
            'provider' => $provider,
        ]);

        Log::info('Payment initiated', ['transaction_id' => $responseData['transactionId'], 'loan_id' => $loan->id, 'payment' => $payment]);
        return ['success' => true, 'message' => 'Payment initiated. Please complete the transaction on your phone.'];
    }
}
