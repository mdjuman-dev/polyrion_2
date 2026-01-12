<?php

namespace App\Livewire\MarketDetails;

use Livewire\Component;

class Chart extends Component
{
    public $event;

    public function mount($event)
    {
        $this->event = $event;
    }

    public function refreshEvent()
    {
        $this->event->refresh();
    }
    
    public function render()
    {
        return view('livewire.market-details.chart');
    }
}