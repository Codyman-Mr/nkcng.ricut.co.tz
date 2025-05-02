<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Controller
{

    public function convertPhoneNumberToInternationalFormat(String $phoneNumber)
    {
        if (preg_match('/^06\d{8}|07\d{8}$/', $phoneNumber)) {
            return $phoneNumber = '+255' . substr($phoneNumber, 1);
        } else {
            return $phoneNumber;
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'phone_number' => 'required',
        ]);

        $enqueue = 1;
        $username = 'MIKE001';
        $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
        $message = $request->message;
        $phoneNumber = $request->phone_number;

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'apiKey' => $apiKey,
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            // 'to' => implode(',', ['+255768591818', '+255756795969']),
            'to' => $phoneNumber,
            'from' => 'NK CNG',
            'message' => $message,
            'enqueue' => $enqueue,
        ]);


        if ($response->successful()) {
            return response()->json(['message' => 'SMS sent successfully!', 'response' => json_decode($response->body())], 200);
        } else {
            return response()->json(['message' => 'Failed to send SMS', 'error' => $response->body()], 500);
        }
    }
}