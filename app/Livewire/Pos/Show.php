<?php

namespace Modules\Pos\Livewire\Pos;

use Livewire\Component;
use Modules\Pos\Models\Pos\Pos;

class Show extends Component
{
    public Pos $pos;

    public function mount(Pos $pos){
        $this->pos = $pos;
    }

    public function render()
    {
        return view('pos::livewire.pos.show')
        ->extends('layouts.app');
    }
}
