<?php

namespace App\Livewire;

use Livewire\Component;

class ConsentModal extends Component
{
    public $showModal = true;

    public function mount()
    {
        if (
            session()->has("user_consent") && session("user_consent") === "accepted"
        ) {
            // remeber to set it to false
            $this->showModal = false; // should be false
        }
    }

    public function accept()
    {
        session()->put('user_consent', 'accepted');
        $this->redirect('/loan-packages'); // Refresh the page
    }

    public function closeModal()
    {
        return redirect('/');
    }


    public function render()
    {
        return session()->has('user_consent')
            ? view('livewire.empty-view') // Create empty view
            : view('livewire.consent-modal');
    }
}
