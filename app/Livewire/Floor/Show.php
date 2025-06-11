<?php

namespace Modules\Pos\Livewire\Floor;

use Livewire\Component;
use Modules\Pos\Models\Floor\FloorPlan;

class Show extends Component
{
    public FloorPlan $floor;

    public function mount(FloorPlan $floor){
        $this->floor = $floor;
    }

    public function render()
    {
        return view('pos::livewire.floor.show')
        ->extends('layouts.app');
    }
}
