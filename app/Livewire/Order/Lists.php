<?php

namespace Modules\Pos\Livewire\Order;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.order.lists')
        ->extends('layouts.app');
    }
}
