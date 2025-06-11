<?php

namespace Modules\Pos\Livewire\Form;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Modules\App\Livewire\Components\Form\Button\StatusBarButton;
use Modules\App\Livewire\Components\Form\Capsule;
use Modules\App\Livewire\Components\Form\Template\SimpleAvatarForm;
use Modules\App\Livewire\Components\Form\Input;
use Modules\App\Livewire\Components\Form\Tabs;
use Modules\App\Livewire\Components\Form\Group;
use Modules\App\Livewire\Components\Form\Table;
use Modules\App\Livewire\Components\Table\Column;
use Modules\App\Livewire\Components\Form\Template\LightWeightForm;
use Modules\Pos\Models\Floor\FloorPlan;
use Modules\Pos\Models\Floor\Table as FloorTable;
use Modules\Pos\Models\Pos\Pos;

class FloorForm extends LightWeightForm
{
    public $floor;
    public $name, $pos;
    public bool $multiple_employee = false, $is_restaurant = true;
    public $posOptions = [];

    protected $rules = [
        'name' => 'required|string|max:60',
        'multiple_employee' => 'boolean|required',
        'is_restaurant' => 'boolean|required',
    ];

    public function mount($floor = null){
        if($floor){
            $this->floor = $floor;
            $this->name = $floor->name;
            $this->pos = $floor->pos_id;
        }

        $this->posOptions = toSelectOptions(Pos::isCompany(current_company()->id)->get(), 'id', 'name');
    }

    public function groups() : array
    {
        return  [
            Group::make('tables',__("Tables"), 'tables')->component('app::form.tab.group.large-table'),
        ];
    }

    public function inputs(): array
    {
        return [
            Input::make('name', "Floor", 'text', 'name', 'top-title', 'none', 'none', __('e.g. Main Floor'))->component('app::form.input.ke-title'),
            Input::make('pos', "Restaurant", 'select', 'pos', 'left', 'none', 'none', "", null, $this->posOptions),
        ];
    }

    public function tables() : array
    {
        return  [
            Table::make('tables',"Tables", 'tables', 'tables', $this->floor->tables ?? null),
        ];
    }

    public function columns() : array
    {
        return  [
            // Tables
            Column::make('table_name',"Name", 'tables'),
            Column::make('shape',"Shape", 'tables')
            ->type('select')
            ->options(['square', 'rectangle', 'circle']),
            Column::make('seats',"Seats", 'tables'),
        ];
    }

    #[On('create-floor')]
    public function createFloor(){

        $this->validate();

        $floor = FloorPlan::create([
            'company_id' => current_company()->id,
            'pos_id' => $this->pos,
            'name' => $this->name,
        ]);

        LivewireAlert::title('Floor Plan saved!')
        ->text('Your floor plan have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('pos-floors.show', $floor->id), navigate: true);
    }
    #[On('update-floor')]
    public function updateFloor(){

        $this->validate();
        $floor = $this->floor;
        $floor->update([
            'name' => $this->name,
            'pos_id' => $this->pos,
        ]);

        LivewireAlert::title('Update saved!')
        ->text('Your changes have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('pos-floors.show', $floor->id), navigate: true);
    }


    public function addRow($tableKey)
    {
        $this->addingRowFor = $tableKey;

        // Default fields for each table type
        if ($tableKey === 'tables') {
            $this->newRow = [
                'table_name' => '',
                'shape' => '',
                'seats' => '',
                'seas' => '',
            ];
        }
    }

    public function saveRow($tableKey)
    {
        $this->validate([
            'floor' => 'required'
        ]);

        $validated = Validator::make($this->newRow, [
            'table_name' => 'required',
            'shape' => 'required|string',
            'seats' => 'required|integer|min:1',
        ])->validate();

        if ($tableKey === 'tables') {
            $table = FloorTable::create(array_merge($validated, [
                'company_id' => current_company()->id,
                'pos_id' => $this->pos,
                'floor_plan_id' => $this->floor->id,
            ]));
            $this->floor->tables = FloorTable::where('floor_plan_id', $this->floor->id)->get(); // refresh
        }

        $this->cancelAddRow();
    }

    public function edit(string $key, int $id)
    {
        $table = collect($this->tables())->firstWhere('key', $key);

        if (!$table || !$table->data) {
            Log::info('Error in editing');
            return;
        }

        $this->editingRowFor = $key;

        $model = collect($table->data)->firstWhere('id', $id);

        // Default fields for each table type
        if ($this->editingRowFor === 'tables' && $model) {
        $this->editRow = collect($model)->toArray(); // populate fields for editing
            $this->editRowId = $id;
        }
    }

    public function updateRow(string $key)
    {
        $validated = $this->editRow;
        // dd($validated);
        if ($key === 'tables') {
            $table = FloorTable::find($this->editRowId);
            $table->update($validated);
            $this->floor->tables = FloorTable::where('floor_plan_id', $this->floor->id)->get(); // refresh
        }

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->editRow = [];
        $this->editRowId = null;
        $this->editingRowFor = null;
    }
}
