<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Product\Product;

class ProductTable extends Table
{
    public array $data = [];


    public function mount(){
        $this->data = ['email', 'phone', 'street'];
    }


    public function showRoute($id) : string
    {
        return route('products.show', ['product' => $id]);
    }

    public function emptyTitle(): string
    {
        return 'No Products Yet';
    }

    public function emptyText(): string
    {
        return 'Define a product for everything you buy or sell whether it’s stockable, consumable, or a service.';
    }

    public function query() : Builder
    {
        $query = Product::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = Product::query()
            ->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        return $query; // Returns a Builder instance for querying the User model
    }

    // List View
    public function columns() : array
    {
        return [
            Column::make('name', __('Product'))->component('app::table.column.special.show-title-link'),
            Column::make('product_reference', 'Internal Reference'),
            Column::make('product_price', 'Sales Price')->component('app::table.column.special.price'),
            Column::make('product_quantity', 'On Hand'),
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
