<?php

namespace Modules\Pos\Livewire\Product;

use Livewire\Component;
use Modules\Pos\Models\Product\Product;

class Show extends Component
{
    public Product $product;

    public function mount(Product $product){
        $this->product = $product;
    }

    public function render()
    {
        return view('pos::livewire.product.show')
        ->extends('layouts.app');
    }
}
