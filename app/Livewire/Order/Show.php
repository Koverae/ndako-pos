<?php

namespace Modules\Pos\Livewire\Order;

use Livewire\Component;

class Show extends Component
{
    public function render()
    {
        return view('pos::livewire.order.show')
        ->extends('layouts.app');
    }
}
