<?php

namespace App\Livewire;

use App\Jobs\SendSmsJob;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Carbon\Carbon;

class SendSms extends Component
{
    public $recipients = '';
    public $message = '';
    public $preview = false;
    public $loanId = '';

    protected $rules = [
        'recipients' => 'required|string',
        'message' => 'nullable|string|max:160',
        'loanId' => 'nullable|integer|exists:loans,id',
    ];

    public function render()
    {
        return view('livewire.send-sms');
    }

    public function sendMessage()
    {
        $this->validate();

        $recipientList = array_filter(array_map('trim', explode(',', $this->recipients)));

        if (empty($recipientList)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No valid recipients provided.',
            ]);
            return;
        }

        try {
            SendSmsJob::dispatch($recipientList, $this->message ?: null, $this->loanId ?: null);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'SMS queued for sending.',
            ]);
            Log::info('SMS job dispatched from SendSms component', ['recipients' => $recipientList, 'loan_id' => $this->loanId]);
        } catch (\Exception $e) {
            Log::error('Failed to queue SMS job: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to queue SMS: ' . $e->getMessage(),
            ]);
        }
    }

    public function previewLoanMessage()
    {
        $this->validate(['loanId' => 'required|integer|exists:loans,id']);

        $loan = Loan::with(['user', 'payments'])->find($this->loanId);
        if (!$loan) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Loan not found.',
            ]);
            return;
        }

        $user = $loan->user;
        $previewMessage = $this->generateLoanReminderMessage($loan, $user);
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Preview: ' . $previewMessage,
        ]);
        $this->preview = true;
    }

    protected function generateLoanReminderMessage(Loan $loan, ?User $user): string
    {
        $user = $user ?? $loan->user;
        $name = $user->first_name ?? 'Customer';

        if ($loan->status === 'approved') {
            return "Habari {$name}, Loan #{$loan->id} approved! Payment of TZS {$loan->loan_required_amount} is being processed.";
        }

        $nextDueDate = $this->calculateNextDueDate($loan);
        return "Habari {$name}, Your next payment reminder for Loan #{$loan->id} is due on {$nextDueDate->format('d/m/Y')}. Please ensure timely payment.";
    }

    protected function calculateNextDueDate(Loan $loan): Carbon
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
}
