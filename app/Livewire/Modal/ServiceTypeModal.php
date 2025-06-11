<?php

namespace Modules\Pos\Livewire\Modal;

use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class ServiceTypeModal extends ModalComponent
{
    public array $services = [];

    public function mount(){
        $this->services = [
            'eat-in' => ['key'=> 'eat-in', 'label' => 'Eat-In', 'icon' => 'fas fa-utensils'],
            'take-away' => ['key'=> 'take-away', 'label' => 'Take-Away', 'icon' => 'bi bi-bag-fill'],
            'in-room' => ['key'=> 'in-room', 'label' => 'In-Room Service', 'icon' => 'bi bi-door-closed-fill'],
        ];
    }

    public function render()
    {
        return view('pos::livewire.modal.service-type-modal');
    }

    public function selectService($type)
    {
        $service = $this->services[$type];

        $this->dispatch('selectServiceType', service: $type);

        $this->dispatch('closeModal');
    }

}
