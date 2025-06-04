<?php

namespace Modules\Pos\Livewire\Navbar\ControlPanel;

use Modules\App\Livewire\Components\Navbar\ControlPanel;
use Modules\App\Livewire\Components\Navbar\SwitchButton;

class ProductCategoryPanel extends ControlPanel
{
    public $category;

    public function mount($category = null, $isForm = false)
    {
        $this->showBreadcrumbs = true;
        $this->generateBreadcrumbs();
        $this->new = route('product-categories.create');

        $this->filterTypes = [
            'status' => [
                'active' => 'active',
                'inactive' => 'inactive',
            ],
        ];
        if($isForm){
            $this->showIndicators = true;
        }

        if($category){
            $this->showIndicators = true;
            $this->category = $category;
            $this->isForm = true;
            $this->currentPage = $category->name;
        }else{
            $this->currentPage = 'Product Categories';
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
