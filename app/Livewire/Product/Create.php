<?php

namespace Modules\Pos\Livewire\Product;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('pos::livewire.product.create')
        ->extends('layouts.app');
    }
}
