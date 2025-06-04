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
use Modules\Pos\Models\Product\ProductCategory;

class ProductCategoryForm extends LightWeightForm
{
    public $category;
    public $name, $pos, $parent, $status;
    public $parentOptions = [], $posOptions = [];

    public function mount($category = null){
        $this->default_img = 'placeholder';
        $this->hasPhoto = true;

        $this->parentOptions = toSelectOptions(ProductCategory::isCompany(current_company()->id)->get(), 'id', 'name');
        $this->posOptions = toSelectOptions(Pos::isCompany(current_company()->id)->get(), 'id', 'name');

        if($category){
            $this->category = $category;
            $this->name = $category->name;
            $this->pos = $category->pos_id;
            $this->parent = $category->parent_id;
            $this->image_path = $category->image_path;
        }
    }

    protected $rules = [
        'name' => 'required|string|max:30',
        'pos' => 'required|integer|exists:pos,id',
        'parent' => 'nullable|integer|exists:product_categories,id'
    ];

    public function inputs(): array
    {
        return [
            Input::make('name', "Product Category", 'text', 'name', 'top-title', 'none', 'none', __('e.g. Hot Drinks'))->component('app::form.input.ke-title'),
            Input::make('category-pos', "Restaurant / Bar", 'select', 'pos', 'left', 'none', 'none', "", null, $this->posOptions),
            Input::make('parent-category', "Parent Category", 'select', 'parent', 'left', 'none', 'none', "", null, $this->parentOptions),
        ];
    }

    public function updatedPhoto(){
        // Validate the uploaded file
        $this->validate();
        if($this->category){
            $category = ProductCategory::find($this->category->id);

            if(!$this->image_path){
                $this->image_path = $category->id . '_product_category.png';

                // $this->photo->storeAs('avatars', $this->image_path, 'public');
                $category->update([
                    'image_path' => $this->image_path,
                ]);
            }

            $this->photo->storeAs('avatars', $this->image_path, 'public');


            // Send success message
            session()->flash('message', 'Image updated successfully!');
        }
    }

    #[On('create-category')]
    public function createCategory(){
        $this->validate();

        $category = ProductCategory::create([
            'company_id' => current_company()->id,
            'name' => $this->name,
            'pos_id' => $this->pos,
            'parent_id' => $this->parent,
        ]);

        $avatar = $category->id.'_product_category.png';
        if($this->photo){
            $this->photo->storeAs('avatars', $avatar, 'public');
        }
        $category->update([
            'image_path' => $avatar,
        ]);

        LivewireAlert::title('Product Category saved!')
        ->text('Your Category have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('product-categories.show', $category->id), navigate: true);

    }

    #[On('update-category')]
    public function updateCategory(){
        $this->validate();
        $category = $this->category;

        if(!$this->image_path){
            $this->image_path = $category->id . '_product_category.png';
        }
        if($this->photo){
            $this->photo->storeAs('avatars', $this->image_path, 'public');
        }

        $category->update([
            'name' => $this->name,
            'parent_id' => $this->parent,
            'pos_id' => $this->pos,
        ]);

        LivewireAlert::title('Product Category saved!')
        ->text('Your Category have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('product-categories.show', $category->id), navigate: true);

    }

}
