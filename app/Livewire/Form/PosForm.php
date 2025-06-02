<?php

namespace Modules\Pos\Livewire\Form;

use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Modules\App\Livewire\Components\Form\Button\StatusBarButton;
use Modules\App\Livewire\Components\Form\Capsule;
use Modules\App\Livewire\Components\Form\Template\SimpleAvatarForm;
use Modules\App\Livewire\Components\Form\Input;
use Modules\App\Livewire\Components\Form\Tabs;
use Modules\App\Livewire\Components\Form\Group;
use Modules\App\Livewire\Components\Form\Table;
use Modules\App\Livewire\Components\Form\Template\LightWeightForm;
use Modules\App\Livewire\Components\Table\Column;
use Modules\App\Traits\Files\HasFileUploads;
use Modules\Pos\Models\Pos\Pos;
use Modules\Pos\Models\Pos\PosSetting;

class PosForm extends LightWeightForm
{
    public $pos;
    public $name, $warehouse;
    public bool $multiple_employee = false, $is_restaurant = true;

    protected $rules = [
        'name' => 'required|string|max:60',
        'multiple_employee' => 'boolean|required',
        'is_restaurant' => 'boolean|required',
    ];

    public function mount($pos = null){
        if($pos){
            $this->pos = $pos;
            $this->name = $pos->name;
            $this->multiple_employee = $pos->multiple_employee ?? true;
            $this->is_restaurant = $pos->is_restaurant ?? true;
        }
    }

    public function groups() : array
    {
        return  [
            // make($key, $label, $tabs = null)
            Group::make('info',"Additional Information", 'none'),
        ];
    }

    public function inputs(): array
    {
        return [
            Input::make('name', "Restaurant", 'text', 'name', 'top-title', 'none', 'none', __('e.g. Mamba Resorts Restaurant'))->component('app::form.input.ke-title'),
            Input::make('is_restaurant',"Is restaurant/bar", 'select', 'is_restaurant', 'left', 'none', 'info')->component('app::form.input.checkbox.simple'),
        ];
    }

    #[On('create-pos')]
    public function createPos(){

        $this->validate();

        $pos = Pos::create([
            'company_id' => current_company()->id,
            'name' => $this->name,
            'has_multiple_employee' => $this->multiple_employee,
            'is_restaurant' => true,
        ]);

        PosSetting::create([
            'company_id' => $pos->company_id,
            'pos_id' => $pos->id,
        ]);

        LivewireAlert::title('Restaurant saved!')
        ->text('Your restaurant have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('pos.show'), navigate: true);
    }

    #[On('update-pos')]
    public function updatePos(){

        $this->validate();

        $this->pos->update([
            'name' => $this->name,
            'has_multiple_employee' => $this->multiple_employee,
            'is_restaurant' => true,
        ]);

        LivewireAlert::title('Update saved!')
        ->text('Your changes have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('pos.show'), navigate: true);
    }
}
