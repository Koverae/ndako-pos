<?php

namespace Modules\Pos\Livewire\Payment;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.payment.lists')
        ->extends('layouts.app');
    }
}
