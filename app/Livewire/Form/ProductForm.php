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
use Modules\Pos\Models\Product\Product;
use Modules\Pos\Models\Product\ProductCategory;
use Modules\RevenueManager\Models\Tax\Tax;

class ProductForm extends LightWeightForm
{
    public $product;
    public $name, $category, $status, $price = 0, $cost = 0, $type = 'goods', $reference, $barcode, $quantity = 0.00;
    public $categoryOptions = [], $productTypeOptions = [], $productSaleTaxes = [], $saleTaxOptions = [], $selectedSaleTax = [];

    public function mount($product = null){
        $this->default_img = 'placeholder';
        $this->hasPhoto = true;

        $this->categoryOptions = toSelectOptions(ProductCategory::isCompany(current_company()->id)->get(), 'id', 'name');
        $types = [
            ['id' => 'goods', 'label' => 'Goods'],
            ['id' => 'service', 'label' => 'Service'],
            ['id' => 'combo', 'label' => 'Combo'],
        ];
        $this->productTypeOptions = toSelectOptions($types, 'id', 'label');

        $this->saleTaxOptions = toSelectOptions(Tax::isCompany(current_company()->id)->isType('sales')->get(), 'id', 'name');

        if($product){
            $this->product = $product;
            $this->name = $product->name;
            $this->category = $product->product_category;
            $this->image_path = $product->image_path;
        }
    }

    protected $rules = [
        'name' => 'required|string|max:70',
        'category' => 'nullable|exists:product_categories,id',
        'price' => 'required|numeric',
        'cost' => 'nullable|numeric',
        'type' => 'required|string|in:goods,service,combo',
        'reference' => 'nullable|string|max:50',
        'barcode' => 'nullable|string|max:100',
    ];

    public function tabs() : array
    {
        return [
            Tabs::make('general',__('General Information')),
        ];
    }

    public function groups() : array
    {
        return [
            Group::make('general',__("General Informations"), 'general'),
            Group::make('accounting',__("Accounting Informations"), 'general'),
        ];
    }

    public function inputs(): array
    {
        return [
            Input::make('name', "Product Name", 'text', 'name', 'top-title', 'none', 'none', __('e.g. Chapati'))->component('app::form.input.ke-title'),
            Input::make('category', "Category", 'select', 'category', 'left', 'none', 'none', "", null, $this->categoryOptions),
            Input::make('product-type', "Product Type", 'select', 'type', 'left', 'general', 'general', "", null, $this->productTypeOptions),
            Input::make('product-price', "Price", 'price', 'price', 'left', 'general', 'general', "", null),
            Input::make('product-cost', "Cost", 'price', 'cost', 'left', 'general', 'general', "", null),
            Input::make('product-reference', "Reference", 'text', 'reference', 'left', 'general', 'general', "", null, $this->categoryOptions),
            Input::make('product-barcode', "Barcode", 'text', 'barcode', 'left', 'general', 'general', "", null, $this->categoryOptions),
            // Taxes
            Input::make('sales-taxes', "Sales Taxes", 'tag', 'selectedSaleTax', 'left', 'general', 'accounting', "", null, ['data' => $this->productSaleTaxes, 'options' => $this->saleTaxOptions, 'action' => 'addSaleTax', 'delete' => 'removeSaleTax']),
        ];
    }

    public function addSaleTax()
    {
        $this->validate([
            'selectedSaleTax' => 'required|exists:taxes,id',
        ]);

        if (is_null($this->product)) {
            // We're on the create page — use array to collect taxes
            if (in_array($this->selectedSaleTax, $this->productSaleTaxes)) {
                session()->flash('error', 'This tax has already been added.');
                return;
            }

            $this->productSaleTaxes[] = $this->selectedSaleTax;
        } else {
            // We're on the edit page — update the product directly
            $existingTaxes = $this->product->sales_taxes ?? [];

            if (in_array($this->selectedSaleTax, $existingTaxes)) {
                session()->flash('error', 'This tax has already been added to this product.');
                return;
            }

            $existingTaxes[] = $this->selectedSaleTax;
            $this->product->sales_taxes = $existingTaxes;
            $this->product->save();

            session()->flash('success', 'Tax added to product.');
        }

        $this->selectedSaleTax = null; // reset dropdown
    }

    public function removeSaleTax($taxId)
    {
        if (is_null($this->product)) {
            // Create mode – work with temporary array
            $this->productSaleTaxes = array_filter(
                $this->productSaleTaxes,
                fn ($id) => $id != $taxId
            );
        } else {
            // Edit mode – update the saved product directly
            $existingTaxes = $this->product->sales_taxes ?? [];

            $filtered = array_filter($existingTaxes, fn ($id) => $id != $taxId);

            $this->product->sales_taxes = array_values($filtered); // reindex to avoid gaps
            $this->product->save();

            session()->flash('success', 'Tax removed from product.');
        }
    }

    public function updatedPhoto(){
        // Validate the uploaded file
        $this->validate();
        if($this->category){
            $category = ProductCategory::find($this->category->id);

            if(!$this->image_path){
                $this->image_path = $category->id . '_product.png';

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

    #[On('create-product')]
    public function createProduct(){
        $this->validate();

        $product = Product::create([
            'company_id' => current_company()->id,
            'name' => $this->name,
            'product_category_id' => $this->category,
            'product-type' => $this->type,
            'product-price' => $this->price,
            'product-cost' => $this->cost,
            'product-reference' => $this->reference,
            'product-code' => $this->barcode,
        ]);

        $product->sales_taxes = $this->productSaleTaxes;

        $avatar = $product->id.'_product.png';

        if($this->photo){
            $this->photo->storeAs('avatars', $avatar, 'public');
        }
        $product->update([
            'image_path' => $avatar,
        ]);

        LivewireAlert::title('Product saved!')
        ->text('Your product have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('products.show', $product->id), navigate: true);

    }

    #[On('update-product')]
    public function updateProduct(){
        $this->validate();
        $product = $this->product;

        if(!$this->image_path){
            $this->image_path = $product->id . '_product.png';
        }
        if($this->photo){
            $this->photo->storeAs('avatars', $this->image_path, 'public');
        }

        $product->update([
            'name' => $this->name,
            'parent_id' => $this->parent,
        ]);

        LivewireAlert::title('Product saved!')
        ->text('Your product have been saved.')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('products.show', $product->id), navigate: true);

    }
}
