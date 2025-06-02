<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Pos\Pos;

class PosTable extends Table
{
    public array $data = [];


    public function mount(){
        $this->view_type = 'kanban';
        $this->data = ['email', 'phone', 'street'];
    }


    public function showRoute($id) : string
    {
        return route('pos.show', ['pos' => $id]);
    }

    public function emptyTitle(): string
    {
        return 'Feeling a Bit Empty Here 🍽️';
    }

    public function emptyText(): string
    {
        return 'Start by adding your first restaurant or bar to manage menus, staff, and services. Once added, it’ll show up here.';
    }

    public function query() : Builder
    {
        $query = Pos::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = Pos::query()
            ->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        return $query; // Returns a Builder instance for querying the User model
    }

    // List View
    public function columns() : array
    {
        return [
            Column::make('name', __('Name'))->component('app::table.column.special.show-title-link'),
            Column::make('email', __('Email')),
            Column::make('street', __('Address')),
        ];
    }

    // Kanban View
    public function cards() : array
    {
        return [
            Card::make('name', "name", "", $this->data)->component('app::table.card.template.pos'),
        ];
    }
}
