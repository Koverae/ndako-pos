<?php

namespace Modules\Pos\Livewire\Product;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.product.lists')
        ->extends('layouts.app');
    }
}
