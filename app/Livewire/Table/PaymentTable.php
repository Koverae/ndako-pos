<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Order\PosOrderPayment;

class PaymentTable extends Table
{
    public array $data = [];


    public function mount(){
        $this->data = ['email', 'phone', 'street'];
    }


    public function showRoute($id) : string
    {
        return "";
    }

    public function emptyTitle(): string
    {
        return 'No Payments Yet';
    }

    public function emptyText(): string
    {
        return 'You haven’t made any payments yet. Once a payment is processed, it will appear here.';
    }

    public function query() : Builder
    {
        $query = PosOrderPayment::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = PosOrderPayment::query()
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
            Column::make('date', 'Date')->component('app::table.column.special.date.basic'),
            Column::make('guest_id', 'Guest')->component('app::table.column.special.contact.simple'),
            Column::make('amount', 'Amount')->component('app::table.column.special.price'),
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
