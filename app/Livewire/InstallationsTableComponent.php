<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Installation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class InstallationsTableComponent extends Component
{

    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';
    public $statusFilter = '';

    public $sort_by = 'id';

    public function mount(){
        abort_unless(auth()->guard()->user()->role === 'admin', 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {

        $query = Installation::with(['customerVehicle.user', 'cylinderType']);

        // Apply search filter
        if ($this->search) {
            $searchTerm = '%' . Str::lower($this->search) . '%';
            

            $query->searchLike(['id', 'status', 'payment_type', 'customerVehicle.user.first_name', ], $searchTerm)->get();
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $installations = $query->latest()->paginate(10);


        return view('livewire.installations-table-component',[
            'installations' => $installations,
        ]);
    }
}
