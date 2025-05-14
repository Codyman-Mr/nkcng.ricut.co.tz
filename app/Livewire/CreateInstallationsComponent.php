<?php

namespace App\Livewire;

use App\Models\CustomerVehicle;
use App\Models\CylinderType;
use App\Models\Installation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;

class CreateInstallationsComponent extends Component
{

    public $userId;
    public $selectedVehicleId;
    public $selectedCylinderTypeId;
    public $status = 'pending';
    public $paymentType = 'loan';
    public $vehicles = [];
    public $cylinderTypes = [];
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'selectedVehicleId' => 'required|exists:customer_vehicles,id',
        'selectedCylinderTypeId' => 'required|exists:cylinder_types,id',
        'status' => 'required|in:pending,completed',
        'paymentType' => 'required|in:direct,loan',
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->loadData();
    }

    public function loadData()
    {
        $this->vehicles = CustomerVehicle::where('user_id', $this->userId)
            ->pluck('plate_number', 'id')
            ->toArray();

        $this->cylinderTypes = CylinderType::pluck('name', 'id')->toArray();
    }

    public function saveInstallation()
    {
        $this->validate();

        $installation = Installation::where('customer_vehicle_id', $this->selectedVehicleId)
            ->where('cylinder_type_id', $this->selectedCylinderTypeId)
            ->first();

        if ($installation) {
            $installation->update([
                'selected_vehicle_id' => $this->selectedVehicleId,
                'selected_cylinder_type_id' => $this->selectedCylinderTypeId,
                'status' => $this->status,
                'payment_type' => $this->paymentType,
            ]);
        }else {
            $installation = Installation::create([
                'customer_vehicle_id' => $this->selectedVehicleId,
                'cylinder_type_id' => $this->selectedCylinderTypeId,
                'status' => $this->status,
                'payment_type' => $this->paymentType,
            ]);
        }

        // Update loan if installation is completed
        if ($this->status === 'completed') {
            $loan = Loan::where('user_id', $this->userId)->first();
            if ($loan) {
                $loan->update(['installation_id' => $installation->id]);
            }
        }

        $this->successMessage = 'Installation saved successfully.';
        $this->errorMessage = '';
        $this->reset(['selectedVehicleId', 'selectedCylinderTypeId', 'status', 'paymentType']);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.create-installations-component');
    }
}
