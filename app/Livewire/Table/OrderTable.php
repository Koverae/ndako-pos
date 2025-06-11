<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Order\PosOrder;

class OrderTable extends Table
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
        return 'No Orders Yet';
    }

    public function emptyText(): string
    {
        return 'You haven’t received any orders yet. Once an order is placed, it will appear here.';
    }

    public function query() : Builder
    {
        $query = PosOrder::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = PosOrder::query()
            ->where('reference', 'like', '%' . $this->searchQuery . '%')
            ->orWhere('receipt_number', 'like', '%' . $this->searchQuery . '%');
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
            Column::make('receipt_number', 'Order Id'),
            Column::make('pos_id', 'Restaurant')->component('app::table.column.special.pos.restaurant'),
            Column::make('table_id', 'Table')->component('app::table.column.special.pos.floor-table'),
            Column::make('cashier_id', 'Cashier')->component('app::table.column.special.contact.user'),
            Column::make('customer_id', 'Guest')->component('app::table.column.special.contact.simple'),
            Column::make('total_amount', 'Total Amount')->component('app::table.column.special.price'),
            // Column::make('product_quantity', 'On Hand'),
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
