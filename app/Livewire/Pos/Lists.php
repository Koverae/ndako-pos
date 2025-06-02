<?php

namespace Modules\Pos\Livewire\Pos;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.pos.lists')
        ->extends('layouts.app');
    }
}
