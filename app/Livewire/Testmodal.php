<?php

namespace App\Livewire;

use Livewire\Component;

class Testmodal extends Component
{

    public $showModal = true;

    public function mount(){
        if (
            session()->has("user_consent") && session("user_consent") === "accepted"
        ){
            $this->showModal = true;
        }
    }

    public function accept(){
        session(["user_consent" => "accepted"]);
        $this->showModal = true;
    }

    public function closeModal(){
        $this->showModal = false;
        return redirect('/');
    }

    public function render()
    {

        return view('livewire.testmodal');
    }
}
