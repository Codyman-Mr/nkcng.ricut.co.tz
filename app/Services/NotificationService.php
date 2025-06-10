<?php

namespace App\Services;

use App\Livewire\SendSms;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Models\GovernmentGuarantor;
use App\Models\PrivateGuarantor;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendApprovalNotifications(User $user, Loan $loan)
    {
        $sms = app(SendSms::class);
        $recipients = [];
        $messages = [];

        if ($user->phone_number) {
            $recipients[] = $user->phone_number;
            $messages[$user->phone_number] = "Habari {$user->first_name}, mkopo wako umeidhinishwa! Tutawasiliana nawe ndani ya siku 7 kwa ajili ya usakinishaji wa silinda. Tafadhali leta nyaraka zote za lazima unapokuja ofisini.";
        } else {
            Log::warning('User phone number missing for SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id]);
        }

        $governmentGuarantor = GovernmentGuarantor::where('loan_id', $loan->id)->first();
        if ($governmentGuarantor && $governmentGuarantor->phone_number) {
            $recipients[] = $governmentGuarantor->phone_number;
            $messages[$governmentGuarantor->phone_number] = "Habari {$governmentGuarantor->first_name}, mkopo wa {$user->first_name} umeidhinishwa. Tafadhali jiandae kwa majukumu yako kama mdhamini.";
        } elseif ($governmentGuarantor) {
            Log::warning('Government guarantor phone number missing for SMS notification', ['guarantor_id' => $governmentGuarantor->id, 'loan_id' => $loan->id]);
        }

        $privateGuarantor = PrivateGuarantor::where('loan_id', $loan->id)->first();
        if ($privateGuarantor && $privateGuarantor->phone_number) {
            $recipients[] = $privateGuarantor->phone_number;
            $messages[$privateGuarantor->phone_number] = "Habari {$privateGuarantor->first_name} {$privateGuarantor->last_name}, mkopo wa {$user->first_name} umeidhinishwa. Tafadhali jiandae kwa majukumu yako kama mdhamini.";
        } elseif ($privateGuarantor) {
            Log::warning('Private guarantor phone number missing for SMS notification', ['guarantor_id' => $privateGuarantor->id, 'loan_id' => $loan->id]);
        }

        $this->sendSmsNotifications($sms, $recipients, $messages);
    }

    public function sendPaymentConfirmationNotifications(User $user, Loan $loan, Payment $payment)
    {
        $sms = app(SendSms::class);
        $recipients = [];
        $messages = [];

        if ($user->phone_number) {
            $recipients[] = $user->phone_number;
            $messages[$user->phone_number] = "Habari {$user->first_name}, malipo yako ya TZS {$payment->amount} kwa mkopo wako (ID: {$loan->id}) yamefanikiwa. Asante!";
        } else {
            Log::warning('User phone number missing for payment SMS notification', ['user_id' => $user->id, 'loan_id' => $loan->id, 'payment_id' => $payment->id]);
        }

        $governmentGuarantor = GovernmentGuarantor::where('loan_id', $loan->id)->first();
        if ($governmentGuarantor && $governmentGuarantor->phone_number) {
            $recipients[] = $governmentGuarantor->phone_number;
            $messages[$governmentGuarantor->phone_number] = "Habari {$governmentGuarantor->first_name}, malipo ya TZS {$payment->amount} kwa mkopo wa {$user->first_name} (ID: {$loan->id}) yamefanikiwa.";
        } elseif ($governmentGuarantor) {
            Log::warning('Government guarantor phone number missing for payment SMS notification', ['guarantor_id' => $governmentGuarantor->id, 'loan_id' => $loan->id, 'payment_id' => $payment->id]);
        }

        $privateGuarantor = PrivateGuarantor::where('loan_id', $loan->id)->first();
        if ($privateGuarantor && $privateGuarantor->phone_number) {
            $recipients[] = $privateGuarantor->phone_number;
            $messages[$privateGuarantor->phone_number] = "Habari {$privateGuarantor->first_name} {$privateGuarantor->last_name}, malipo ya TZS {$payment->amount} kwa mkopo wa {$user->first_name} (ID: {$loan->id}) yamefanikiwa.";
        } elseif ($privateGuarantor) {
            Log::warning('Private guarantor phone number missing for payment SMS notification', ['guarantor_id' => $privateGuarantor->id, 'loan_id' => $loan->id, 'payment_id' => $payment->id]);
        }

        $this->sendSmsNotifications($sms, $recipients, $messages);
    }

    protected function sendSmsNotifications($sms, $recipients, $messages)
    {
        if (!empty($recipients)) {
            try {
                $successCount = 0;
                foreach ($recipients as $recipient) {
                    $success = $sms->send($recipient, $messages[$recipient]);
                    if ($success) {
                        $successCount++;
                        Log::info('SMS notification sent successfully', ['recipient' => $recipient]);
                    } else {
                        Log::error('Failed to send SMS notification', ['recipient' => $recipient]);
                    }
                }

                if ($successCount > 0) {
                    Log::info('Sent SMS notifications', ['successful' => $successCount, 'total' => count($recipients)]);
                }
            } catch (\Exception $e) {
                Log::error('Error sending SMS notifications: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        } else {
            Log::warning('No valid phone numbers for SMS notifications');
        }
    }
}
