<?php

namespace Modules\Pos\Livewire\ProductCategory;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('pos::livewire.product-category.create')
        ->extends('layouts.app');
    }
}
