<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Loan;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|min:1000',
            'phone_number' => ['required', 'regex:/^(0|\+255)?[6-7][0-9]{8}$/'],
            'provider' => 'required|in:Mpesa,TigoPesa,AirtelMoney,HaloPesa',
        ]);

        $paymentService = new PaymentService();
        $result = $paymentService->initiatePayment(
            $request->loan_id,
            $request->amount,
            $request->phone_number,
            $request->provider
        );

        if ($result['success']) {
            return response()->json(['message' => $result['message']]);
        } else {
            return response()->json(['error' => $result['message']], 400);
        }
    }

    public function handleCallback(Request $request)
    {
        Log::info('AzamPay callback received', ['data' => $request->all()]);

        $data = $request->all();
        $transactionId = $data['transactionId'] ?? null;
        $status = $data['status'] ?? 'failed';

        if (!$transactionId) {
            Log::error('Invalid callback data', ['data' => $data]);
            return response()->json(['message' => 'Invalid callback'], 400);
        }

        $payment = \App\Models\Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::error('Payment not found', ['transaction_id' => $transactionId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        try {
            $payment->update([
                'status' => $status === 'success' ? 'completed' : 'failed',
                'callback_data' => json_encode($data),
            ]);

            if ($status === 'success') {
                $loan = Loan::find($payment->loan_id);
                $loanApproval = new \App\Livewire\LoanApproval();
                $loanApproval->loan = $loan;
                $loanApproval->sendPaymentConfirmationNotifications($loan->user, $loan, $payment);

                Log::info('Payment completed', ['transaction_id' => $transactionId, 'loan_id' => $payment->loan_id]);
            } else {
                Log::warning('Payment failed', ['transaction_id' => $transactionId, 'loan_id' => $payment->loan_id]);
            }

            return response()->json(['message' => 'Callback processed'], 200);
        } catch (\Exception $e) {
            Log::error('Callback processing error: ' . $e->getMessage(), ['transaction_id' => $transactionId]);
            return response()->json(['message' => 'Error processing callback'], 500);
        }
    }

    public function calculateNextDueDate(Loan $loan)
    {
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
    $today = Carbon::today();
    $next7Days = Carbon::today()->addDays(7);

    // Chagua loans zenye status 'approved' na loan_end_date ndani ya next 7 days
    $dueSoonLoans = Loan::where('status', 'approved')
        ->whereBetween('loan_end_date', [$today, $next7Days])
        ->get();

    return view('loan.repayment-alerts', compact('dueSoonLoans'));
}

    public function paymentHistory(Loan $loan)
    {
        return view(
            'payments.payment-history',
            ['loan'=>$loan->load(['payments'])]
        );
    }


}
