<?php

namespace Modules\Pos\Livewire\Floor;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('pos::livewire.floor.create')
        ->extends('layouts.app');
    }
}
