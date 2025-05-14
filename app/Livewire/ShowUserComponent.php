<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Loan;
use App\Models\CustomerVehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\GpsDevice;

class ShowUserComponent extends Component
{
    public $user;
    public $userId;
    public $deviceId;
    public $isEditing = false;
    public $first_name = 'John';
    public $last_name = 'Doe';
    public $phone_number = '+1234567890';
    public $gender = 'Male';
    public $dob = '1990-01-01';
    public $nida_number = '1234567890123456';
    public $address = '123 Street, City';
    public $loan;
    public $vehicle;
    public $vehicleStatus = 'on';
    public $user_gps;
    public $gpsDevice;

    public $gpsDeviceId;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::find($userId);
        
        $this->loadData();

        if ($this->user) {
            // Load loan
            $this->loan = Loan::where('user_id', $this->userId)->first();

            // Load vehicle
            $this->vehicle = CustomerVehicle::where('user_id', $this->userId)->first();

            // Load GPS device (assuming vehicle_id relationship)
            $this->gpsDevice = $this->vehicle
                ? GpsDevice::where('vehicle_id', $this->vehicle->id)->first()
                : null;

            // $this->gpsDeviceId = $this->gpsDevice ? $this->gpsDevice->device_id : null;

            $this->gpsDeviceId = $this->gpsDevice->device_id ?? null;

            // Fallback: If no vehicle_id, use assigned_to (optional)
            if (!$this->gpsDevice) {
                $this->gpsDevice = GpsDevice::where('assigned_to', $this->userId)->first();
            }
        } else {
            $this->loan = null;
            $this->vehicle = null;
            $this->gpsDevice = null;
        }
    }

    public function loadData()
    {
        $this->user = User::find($this->userId);

        if ($this->user) {
            $this->loan = Loan::where('user_id', $this->userId)->first();
            $this->vehicle = CustomerVehicle::where('user_id', $this->userId)->first();
            $this->gpsDevice = $this->vehicle
                ? GpsDevice::where('vehicle_id', $this->vehicle->id)->first()
                : null;

            if (!$this->gpsDevice) {
                $this->gpsDevice = GpsDevice::where('assigned_to', $this->userId)->first();
            }
        } else {
            $this->loan = null;
            $this->vehicle = null;
            $this->gpsDevice = null;
        }
    }

    public function refreshGpsDevice()
    {
        $this->loadData();
    }

    public function toggleVehicleStatus()
    {
        $this->vehicleStatus = $this->vehicleStatus === 'on' ? 'off' : 'on';
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
    }

    public function save()
    {
        // Validate & Save logic here
        $this->isEditing = false;
    }



    public function render()
    {
        return view('livewire.show-user-component', [
            'user' => $this->user,
            'userId' => $this->userId,
            'deviceId' => $this->deviceId,
            'loan' => $this->loan,
            'vehicle' => $this->vehicle,
            'gpsDeviceId' => $this->gpsDeviceId,
        ]);
    }
}
