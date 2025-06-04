<?php

namespace Modules\Pos\Livewire\ProductCategory;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.product-category.lists')
        ->extends('layouts.app');
    }
}
