<?php

namespace Modules\Pos\Livewire\Pos;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('pos::livewire.pos.create')
        ->extends('layouts.app');
    }
}
