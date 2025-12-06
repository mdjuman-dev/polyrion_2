<?php

namespace App\Livewire\MarketDetails;

use Livewire\Component;

class TradingPanel extends Component
{
    public $event;

    public function mount($event)
    {
        $this->event = $event;
    }


    public function render()
    {
        return view('livewire.market-details.trading-panel');
    }
}
