<?php

namespace Modules\Pos\Livewire\Floor;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.floor.lists')
        ->extends('layouts.app');
    }
}
