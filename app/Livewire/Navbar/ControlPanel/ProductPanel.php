<?php

namespace Modules\Pos\Livewire\Navbar\ControlPanel;

use Modules\App\Livewire\Components\Navbar\ControlPanel;
use Modules\App\Livewire\Components\Navbar\SwitchButton;

class ProductPanel extends ControlPanel
{
    public $product;

    public function mount($product = null, $isForm = false)
    {
        $this->showBreadcrumbs = true;
        $this->generateBreadcrumbs();
        $this->new = route('products.create');

        $this->filterTypes = [
            'status' => [
                'active' => 'active',
                'inactive' => 'inactive',
            ],
        ];
        if($isForm){
            $this->showIndicators = true;
        }

        if($product){
            $this->showIndicators = true;
            $this->product = $product;
            $this->isForm = true;
            $this->currentPage = $product->product_name;
        }else{
            $this->currentPage = 'Products';
        }

    }

    public function switchButtons() : array
    {
        return  [
            // make($key, $label)
            SwitchButton::make('lists',"switchView('lists')", "bi-list-task"),
            SwitchButton::make('kanban',"switchView('kanban')", "bi-kanban"),
        ];
    }
}
