<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Loan;
use App\Models\User;

class PendingLoansTable extends Component
{
    use WithPagination;

    public $search = '';
    protected $updatesQueryString = ['search', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();


        // more direct
        $loans = Loan::where('status', 'pending')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->paginate(15)
            ->onEachSide(2);

        return view('livewire.pending-loans-table', [
            'loans' => $loans,
            'user' => $user,
        ]);
    }
}
