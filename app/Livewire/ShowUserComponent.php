<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Loan;
use App\Models\CustomerVehicle;
use App\Models\LoanDocument;
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
    public $documents = [];

    protected $documentDisplayNames = [
        'mktaba_wa_mkopo' => 'Loan Agreement',
        'kitambulisho_mwomba_mbele' => 'Applicant\'s ID Front',
        'kitambulisho_mdhamini_1_mbele' => 'Guarantor 1 ID Front',
        'kitambulisho_mdhamini_2_mbele' => 'Guarantor 2 ID Front',
        'leseni_mwomba' => 'Applicant\'s License',
        'kadi_ya_usafiri' => 'Travel Card',
        'barua_ya_utambulisho' => 'Introduction Letter',
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->loadData();

        if ($this->user) {
            // Load loan
            $this->loan = Loan::where('user_id', $this->userId)->first();

            // Load vehicle
            $this->vehicle = CustomerVehicle::where('user_id', $this->userId)->first();

            // Load GPS device
            $this->gpsDevice = $this->vehicle
                ? GpsDevice::where('vehicle_id', $this->vehicle->id)->first()
                : null;

            $this->gpsDeviceId = $this->gpsDevice ? $this->gpsDevice->device_id : null;

            // Fallback: If no vehicle_id, use assigned_to
            if (!$this->gpsDevice) {
                $this->gpsDevice = GpsDevice::where('assigned_to', $this->userId)->first();
                $this->gpsDeviceId = $this->gpsDevice ? $this->gpsDevice->device_id : null;
            }

            // Load documents if loan is approved
            if ($this->loan && $this->loan->status === 'approved') {
                $this->documents = LoanDocument::where('loan_id', $this->loan->id)->get()->map(function ($document) {
                    $document->display_name = $this->documentDisplayNames[$document->document_type] ?? ucwords(str_replace('_', ' ', $document->document_type));
                    return $document;
                });
            }
        } else {
            $this->loan = null;
            $this->vehicle = null;
            $this->gpsDevice = null;
            $this->documents = [];
        }

        Log::info('ShowUserComponent mounted', [
            'user_id' => $this->userId,
            'has_approved_loan' => $this->loan && $this->loan->status === 'approved',
            'document_count' => count($this->documents),
        ]);
    }

    public function loadData()
    {
        $this->user = User::find($this->userId);

        if ($this->user) {
            $this->first_name = $this->user->first_name;
            $this->last_name = $this->user->last_name;
            $this->phone_number = $this->user->phone_number;
            $this->gender = $this->user->gender;
            $this->dob = $this->user->dob;
            $this->nida_number = $this->user->nida_number;
            $this->address = $this->user->address;
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
        if ($this->isEditing) {
            $this->loadData();
        }
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
            'documents' => $this->documents,
        ]);
    }
}
