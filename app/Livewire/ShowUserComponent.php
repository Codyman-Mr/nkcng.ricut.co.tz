<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Loan;
use App\Models\CustomerVehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::find($userId);

        if ($this->user) {
            $user_gps = User::with('gpsDevice')->findOrFail($userId);
            $this->deviceId = $user_gps->gpsDevice ? $user_gps->gpsDevice->device_id : null;
            $this->loan = Loan::where('user_id', $this->userId)->first();
            $this->vehicle = CustomerVehicle::where('user_id', $this->userId)->first();
        } else {
            $this->deviceId = null;
            $this->loan = null;
            $this->vehicle = null;
        }
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
        ]);
    }
}