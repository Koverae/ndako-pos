<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Floor\FloorPlan;

class FloorTable extends Table
{
    public array $data = [];


    public function mount(){
        $this->data = ['email', 'phone', 'street'];
    }


    public function showRoute($id) : string
    {
        return route('pos-floors.show', ['floor' => $id]);
    }

    public function emptyTitle(): string
    {
        return 'No Floor Plans Yet';
    }

    public function emptyText(): string
    {
        return 'Create a floor plan to visualize your restaurant layout and manage your tables efficiently.';
    }

    public function query() : Builder
    {
        $query = FloorPlan::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = FloorPlan::query()
            ->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        // 🎯 Filters
        if (!empty($this->filters)) {
            foreach ($this->filters as $field => $value) {
                $query->where($field, $value);
            }
        }

        return $query; // Returns a Builder instance for querying the User model
    }

    // List View
    public function columns() : array
    {
        return [
            Column::make('name', __('Name'))->component('app::table.column.special.show-title-link'),
            Column::make('pos_id', 'Restaurant')->component('app::table.column.special.pos.restaurant'),
        ];
    }

    // Kanban View
    public function cards() : array
    {
        return [
            Card::make('name', "name", "", $this->data),
        ];
    }
}
