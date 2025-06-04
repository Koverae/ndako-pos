<?php

namespace Modules\Pos\Livewire\Table;

use Modules\App\Livewire\Components\Table\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Modules\App\Livewire\Components\Table\Card;
use Modules\App\Livewire\Components\Table\Column;
use Modules\Pos\Models\Product\ProductCategory;

class ProductCategoryTable extends Table
{
    public array $data = [];


    public function mount(){
        $this->view_type = 'kanban';
        $this->data = ['email', 'phone', 'street'];
    }


    public function showRoute($id) : string
    {
        return route('product-categories.show', ['category' => $id]);
    }

    public function emptyTitle(): string
    {
        return 'No Product Categories';
    }

    public function emptyText(): string
    {
        return 'Organize your products by creating categories. This helps with filtering, reporting, and easier management.';
    }

    public function query() : Builder
    {
        $query = ProductCategory::query();

        // Apply the search query filter if a search query is present
        if ($this->searchQuery) {
            // Search both the booking's name and the related guest's name
            $query = ProductCategory::query()
            ->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        return $query; // Returns a Builder instance for querying the User model
    }

    // List View
    public function columns() : array
    {
        return [
            Column::make('name', __('Product Category'))->component('app::table.column.special.show-title-link'),
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
