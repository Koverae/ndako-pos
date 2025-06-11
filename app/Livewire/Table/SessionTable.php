<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Pos\PosSession;

class SessionTable extends Table
{
    public array $data = [];


    public function mount(){
        $this->data = ['email', 'phone', 'street'];
    }


    public function showRoute($id) : string
    {
        return route('orders.show', ['order' => $id]);
    }

    public function emptyTitle(): string
    {
        return 'No Sessions Yet';
    }

    public function emptyText(): string
    {
        return 'You haven’t started any session yet. Once an session is launched, it will appear here.';
    }

    public function query() : Builder
    {
        $query = PosSession::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = PosSession::query()
            ->where('reference', 'like', '%' . $this->searchQuery . '%');
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
            Column::make('reference', __('Reference')),
            Column::make('pos_id', 'Restaurant')->component('app::table.column.special.pos.restaurant'),
            Column::make('start_date', 'Start Date')->component('app::table.column.special.date.basic'),
            Column::make('starting_balance', 'Total Amount')->component('app::table.column.special.price'),
            Column::make('closing_date', 'Closing Date')->component('app::table.column.special.date.basic'),
            Column::make('closing_balance', 'Total Amount')->component('app::table.column.special.price'),
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
