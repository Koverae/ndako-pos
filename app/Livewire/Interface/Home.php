<?php

namespace Modules\Pos\Livewire\Interface;

use Livewire\Component;
use Modules\Pos\Models\Pos\Pos;
use Modules\Pos\Models\Product\Product;
use Modules\Pos\Models\Product\ProductCategory;

class Home extends Component
{
    public Pos $pos;

    public $tab = 'pay';
    public $productCategoryOptions = [], $productOptions = [];

    public function mount(Pos $pos){
        $this->pos = $pos;
        $this->productCategoryOptions = ProductCategory::isCompany(current_company()->id)->get();
        $this->productOptions = Product::isCompany(current_company()->id)->get();
    }

    public function changeTab($tab){
        $this->tab = $tab;
    }

    public function render()
    {
        return view('pos::livewire.interface.home')
        ->extends('layouts.pos');
    }
}
