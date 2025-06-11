<?php

namespace Modules\Pos\Livewire\Navbar\ControlPanel;

use Modules\App\Livewire\Components\Navbar\Button\ActionButton;
use Modules\App\Livewire\Components\Navbar\ControlPanel;
use Modules\App\Livewire\Components\Navbar\SwitchButton;

class PosPanel extends ControlPanel
{
    public $pos;

    public function mount($pos = null, $isForm = false)
    {
        $this->showBreadcrumbs = true;
        $this->generateBreadcrumbs();
        $this->new = route('properties.create');

        $this->filterTypes = [
            'status' => [
                'active' => 'active',
                'inactive' => 'inactive',
                'under-maintenance' => 'under-maintenance',
            ],
        ];
        if($isForm){
            $this->showIndicators = true;
        }

        if($pos){
            $this->showIndicators = true;
            $this->pos = $pos;
            $this->isForm = true;
            $this->currentPage = $pos->name;
        }else{
            $this->currentPage = $isForm ? 'New' : "Overview";
        }

    }

    public function switchButtons() : array
    {
        return  [
            // make($key, $label)
            // SwitchButton::make('lists',"switchView('lists')", "bi-list-task"),
            SwitchButton::make('kanban',"switchView('kanban')", "bi-kanban"),
        ];
    }
}
