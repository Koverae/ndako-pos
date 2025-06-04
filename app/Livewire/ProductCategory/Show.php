<?php

namespace Modules\Pos\Livewire\ProductCategory;

use Livewire\Component;
use Modules\Pos\Models\Product\ProductCategory;

class Show extends Component
{
    public ProductCategory $category;

    public function mount(ProductCategory $category){
        $this->category = $category;
    }

    public function render()
    {
        return view('pos::livewire.product-category.show')
        ->extends('layouts.app');
    }
}
