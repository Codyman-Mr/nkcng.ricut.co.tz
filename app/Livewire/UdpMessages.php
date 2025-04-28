<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UdpMessage;

class UdpMessages extends Component
{
    public function render()
    {
        return view('livewire.udp-messages', [
            'messages' => UdpMessage::latest()->get()
        ]);
    }
}
