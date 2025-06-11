<?php

namespace Modules\Pos\Livewire\Session;

use Livewire\Component;

class Lists extends Component
{
    public function render()
    {
        return view('pos::livewire.session.lists')
        ->extends('layouts.app');
    }
}
