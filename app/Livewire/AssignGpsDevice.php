<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GpsDevice;
use App\Models\CustomerVehicle;
use App\Models\Loan;
use App\Models\Installation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssignGpsDevice extends Component
{
    public $userId;
    public $selectedDeviceId;
    public $selectedVehicleId;
    public $vehicles = [];
    public $devices = [];
    public $successMessage = '';
    public $errorMessage = '';
    public $statusMessage = '';
    public $canAssign = false;
    public $installationPending = false;
    public $installationRoute = '';

    public $installationId = null;

    public $customerVehicleId = null;

    protected $rules = [
        'selectedDeviceId' => 'required|exists:gps_devices,device_id',
        'selectedVehicleId' => 'nullable|exists:customer_vehicles,id',
    ];

    public function mount($userId)
    {
        $this->userId = $userId;

        $this->checkAssignmentEligibility();
        if ($this->canAssign) {
            $this->loadData();
        }
    }

    public function checkAssignmentEligibility()
    {
        // Check for loan
        $loan = Loan::where('user_id', $this->userId)->first();

        if (!$loan) {
            $this->statusMessage = 'User has not applied for a loan.';
            return;
        }

        if ($loan->status === 'pending') {
            $this->statusMessage = 'Loan is pending approval.';
            return;
        }

        if ($loan->status === 'rejected') {
            $this->statusMessage = 'Loan was rejected.';
            return;
        }

        $this->customerVehicleId = CustomerVehicle::where('user_id', $this->userId)->first()->id ?? null;
        if (!$this->customerVehicleId) {
            $this->statusMessage = 'User has no vehicles.';
            return;
        }

        // Loan is approved, check installation
        $installation = Installation::where('id', $loan->installation_id)->first();

        $this->installationId = Installation::where('customer_vehicle_id', $this->customerVehicleId)->first()->id ?? null;
        if (!$this->installationId) {
            $this->statusMessage = 'User has no installations.';
            return;
        }
        if (!$installation || $installation->status === 'pending') {
            $this->statusMessage = 'Installation is pending. Approve installation to assign a GPS device.';
            $this->installationPending = true;
            // $this->installationRoute = route('installation.create', ['userId' => $this->userId]);
            return;
        }

        // All conditions met
        $this->canAssign = true;
    }

    public function loadData()
    {
        // Load unassigned GPS devices
        $this->devices = GpsDevice::where('assignment_status', 'unassigned')
            ->pluck('device_id', 'device_id')
            ->toArray();

        // Load user's vehicles
        $this->vehicles = CustomerVehicle::where('user_id', $this->userId)
            ->pluck('plate_number', 'id')
            ->toArray();
    }

    public function assignDevice()
    {
        if (!Auth::user()->role === 'admin') {
            $this->errorMessage = 'Unauthorized action.';
            Log::warning('Unauthorized GPS assignment attempt', ['user_id' => Auth::id()]);
            return;
        }
        if (!$this->canAssign) {
            $this->errorMessage = 'Cannot assign device due to eligibility restrictions.';
            return;
        }

        $this->validate();

        $device = GpsDevice::where('device_id', $this->selectedDeviceId)
            ->where('assignment_status', 'unassigned')
            ->first();

        if (!$device) {
            $this->errorMessage = 'Selected device is no longer available.';
            return;
        }

        $device->update([
            'assigned_to' => $this->userId,
            'vehicle_id' => $this->selectedVehicleId ?: null,
            'assignment_status' => 'assigned',
            'assigned_by' => Auth::user()->id,
            'assigned_at' => now(),
        ]);

        Log::info('GPS device assigned', [
            'device_id' => $this->selectedDeviceId,
            'user_id' => $this->userId,
            'vehicle_id' => $this->selectedVehicleId,
        ]);

        $this->successMessage = 'GPS device assigned successfully.';
        $this->errorMessage = '';
        $this->selectedDeviceId = null;
        $this->selectedVehicleId = null;
        $this->loadData();
        $this->dispatch('deviceAssigned');
    }

    public function render()
    {
        return view('livewire.assign-gps-device');
    }
}
