<?php

namespace Modules\Pos\Livewire\Interface;

use Illuminate\Support\Str;
use Livewire\Component;
use Modules\Pos\Models\Pos\Pos;
use Modules\Pos\Models\Product\Product;
use Modules\Pos\Models\Product\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Modules\ChannelManager\Models\Guest\Guest;
use Modules\Pos\Models\Floor\FloorPlan;
use Modules\Pos\Models\Floor\Table;
use Modules\Pos\Models\Order\PosOrder;
use Modules\Pos\Models\Order\PosOrderDetail;

class Home extends Component
{
    public Pos $pos;
    public string $interface = 'tables', $tab = 'pay', $calculatorMode = 'qty';
    public ?int $selectedCategoryId = null, $selectedProductId = null, $selectedPlanId = null, $selectedCustomerId = null;
    public Collection $productCategoryOptions;
    public Collection $productOptions;
    public Collection $orders;
    public Collection $customers;
    public ?PosOrder $order = null;
    public ?Guest $guest = null;

    public $floorPlanOptions, $selectedTable = null;
    public array $cart = [], $selectedService = [];
    public array $services = [
        'eat-in' => ['key'=> 'eat-in', 'label' => 'Eat-In', 'icon' => 'fas fa-utensils'],
        'take-away' => ['key'=> 'take-away', 'label' => 'Take-Away', 'icon' => 'bi bi-bag-fill'],
        'in-room' => ['key'=> 'in-room', 'label' => 'In-Room Service', 'icon' => 'bi bi-door-closed-fill'],
    ];

    public float $cartTotal = 0, $cartTax = 0;
    public $calculatorInput = 0;
    public string $searchQuery = '', $customerSearch = '';
    public string $orderStatusFilter = '', $paymentStatusFilter = '';
    public bool $isLocked = false; // New property for lock state

    public function mount(Pos $pos)
    {
        $this->pos = $pos;
        // Load cart and table from session
        // Load session data with default structure
        $sessionKey = "pos_cart_{$this->pos->id}";
        $sessionData = session()->get($sessionKey, ['cart' => [], 'table_id' => null, 'active_order_id' => null]);

        // Handle legacy or invalid session data
        if (!is_array($sessionData) || !isset($sessionData['cart']) || !array_key_exists('table_id', $sessionData) || !array_key_exists('active_order_id', $sessionData)) {
            $cart = is_array($sessionData) && !array_key_exists('cart', $sessionData) ? $sessionData : [];
            $sessionData = ['cart' => $cart, 'table_id' => null, 'active_order_id' => null];
            session()->put($sessionKey, $sessionData);
        }

        $this->cart = $sessionData['cart'];
        $this->selectedTable = $sessionData['table_id'] ? Table::find($sessionData['table_id']) : null;
        $this->order = $sessionData['active_order_id'] ? PosOrder::find($sessionData['active_order_id']) : null;

        if ($this->order) {
            $this->syncCartWithOrder();
        }

        $this->loadFloors();
        $this->loadCategories();
        $this->loadProducts();
        $this->loadOrders();
        $this->loadCustomers();
        $this->recalculateTotals();
        $this->loadActiveOrder();
    }

    /**
     * Change the active tab.
     */
    public function changeTab(string $tab): void
    {
        $this->tab = $tab;
    }

    /**
     * Change the active interface.
     */
    public function changeInterface(string $interface): void
    {
        $this->interface = $interface;
        if ($interface === 'orders') {
            $this->loadOrders();
        }
    }

    /**
     * Change the active floorPlan.
     */
    public function changeFloorPlan($floorPlan): void
    {
        $this->selectedPlanId = $floorPlan;
    }

    /**
     * Select product category and load related products.
     */
    public function selectCategory($categoryId): void
    {
        $this->selectedCategoryId = $categoryId ?: null;
        $this->loadProducts();
    }

    /**
     * Select table and load or create order.
     */


    public function selectTable($tableId): void
    {
        $table = Table::find($tableId);
        if (!$table) {
            LivewireAlert::title('Table not found!')
                ->text('Selected table does not exist.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }

        $this->selectedTable = $table;
        $this->order = PosOrder::where('pos_id', $this->pos->id)
            ->where('company_id', current_company()->id)
            ->where('table_id', $tableId)
            ->where('status', 'ongoing')
            ->first();

        $this->selectServiceType('eat-in');
        if ($this->order) {
            $this->syncCartWithOrder();
            $this->selectedCustomerId = $this->order->customer_id;
        } elseif (!empty($this->cart)) {
            $this->createOrder();
        }

        $table->update(['status' => 'occupied']);
        $this->interface = 'register';
        $this->saveCartToSession();

        LivewireAlert::title('Table assigned!')
            ->text("Order assigned to {$table->table_name}")
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    /**
     * Select product on checkout.
     */
    public function selectProduct($productId): void
    {
        // Deselect if the same product is clicked
        if ($this->selectedProductId == $productId) {
            $this->selectedProductId = null;
            $this->calculatorInput = '';
            return;
        }

        // Select new product
        $this->calculatorInput = '';
        $this->selectedProductId = $productId;

        // Set calculator input if product exists in cart
        $item = $this->cart[$productId] ?? null;

        if ($item) {
            $this->calculatorInput = match ($this->calculatorMode) {
                'qty' => $item['quantity'],
                'price' => $item['unit_price'],
                'discount' => $item['discount'],
                default => '',
            };
        } else {
            $this->calculatorInput = '';
        }
    }

    public function selectOrder($orderId): void
    {
        $this->order = PosOrder::find($orderId);
        if (!$this->order) {
            LivewireAlert::title('Order not found!')
                ->text('Selected order does not exist.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }

        $this->selectedTable = $this->order->table_id ? Table::find($this->order->table_id) : null;
        $this->selectedCustomerId = $this->order->customer_id;
        $this->syncCartWithOrder();
        $this->interface = 'register';
        $this->saveCartToSession();

        LivewireAlert::title('Order selected!')
            ->text('Order is now active.')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    public function releaseTable($tableId): void
    {
        $table = Table::find($tableId);
        if ($table && $table->status !== 'available') {
            $table->update(['status' => 'available']);
            if ($this->selectedTable?->id === $tableId) {
                $this->selectedTable = null;
                $this->order = null;
                $this->cart = [];
                $this->recalculateTotals();
                $this->saveCartToSession();
            }
            LivewireAlert::title('Table released!')
                ->text("Table {$table->table_name} is now available.")
                ->success()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
        }
    }


    /**
     * Save cart and table to session.
     */
    protected function saveCartToSession(): void
    {
        session()->put("pos_cart_{$this->pos->id}", [
            'cart' => $this->cart,
            'table_id' => $this->selectedTable?->id,
            'active_order_id' => $this->order?->id,
        ]);
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            LivewireAlert::title('Product not found!')
            ->text('Product selected does not exist')
            ->error()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
            return;
        }
        // if ($product->quantity <= 0) {
        //     $this->dispatch('alert', type: 'error', message: 'Product out of stock.');
        //     return;
        // }

        if (!$this->order) {
            $this->createOrder();
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            PosOrderDetail::where('pos_order_id', $this->order->id)
                ->where('product_id', $productId)
                ->update([
                    'quantity' => $this->cart[$productId]['quantity'],
                    'sub_total' => $this->cart[$productId]['quantity'] * $this->cart[$productId]['unit_price'],
                    ]);
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->product_name,
                'unit_price' => $product->product_price,
                'quantity' => 1,
                'discount' => 0
            ];
            PosOrderDetail::create([
                'pos_order_id' => $this->order->id,
                'company_id' => current_company()->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->product_price,
                'sub_total' => $product->product_price,
                'product_discount_amount' => 0,
            ]);
        }

        if ($this->selectedTable) {
            $this->selectedTable->update(['status' => 'occupied']);
        }

        $this->recalculateTotals();
        $this->order->update([
            'total_amount' => $this->cartTotal,
            // 'tax_amount' => $this->cartTax,
        ]);
        $this->saveCartToSession();

        LivewireAlert::title('Product added!')
            ->text('Product added to cart')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    /**
     * Remove product from cart.
     */
    public function removeFromCart($productId): void
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            PosOrderDetail::where('pos_order_id', $this->order->id)
                ->where('product_id', $productId)
                ->delete();
            if(empty($this->cart)) {
                $this->order->delete();
                $this->order = null;
            } else {
                $this->recalculateTotals();
                $this->order->update([
                    'total_amount' => $this->cartTotal,
                    // 'tax_amount' => $this->amountTax,
                ]);
            }
            $this->saveCartToSession();
            LivewireAlert::title('Product removed!')
                ->text('Product removed from cart')
                ->success()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
        }
    }

    /**
     * Update product quantity.
     */
    public function updateQuantity($productId, $quantity): void
    {
        if (isset($this->cart[$productId])) {
            $quantity = max(1, (int) $quantity);
            $product = Product::find($productId);
            // if ($quantity > $product->quantity) {
            //     LivewireAlert::title('Stock limit exceeded!')
            //         ->text('Cannot set quantity beyond available stock.')
            //         ->error()
            //         ->position('top-end')
            //         ->timer(4000)
            //         ->toast()
            //         ->show();
            //     return;
            // }

            $this->cart[$productId]['quantity'] = $quantity;
            PosOrderDetail::where('pos_order_id', $this->order->id)
                ->where('product_id', $productId)
                ->update([
                    'quantity' => $quantity
                    ]);
            $this->recalculateTotals();
            $this->order->update([
                'total_amount' => $this->cartTotal,
                // 'tax_amount' => $this->cartTax,
            ]);
            $this->saveCartToSession();
        }
    }

    /**
     * Cancel the cart and delete order if exists.
     */
    public function cancelOrder(){

        if ($this->order) {
            $this->order->details()->delete();
            $this->order->delete();
            $this->order = null;
        }
        $this->resetCart();
        $this->interface = 'tables';
    }

    /**
     * Reset the cart and delete order if exists.
     */
    public function resetCart(): void
    {
        // if ($this->order) {
        //     $this->order->details()->delete();
        //     $this->order->delete();
        //     $this->order = null;
        // }

        $this->cart = [];
        $this->selectedTable = null;
        $this->selectedCustomerId = null;
        $this->guest = null;
        $this->recalculateTotals();
        $this->saveCartToSession();
        LivewireAlert::title('Cart reset!')
            ->text('Cart has been cleared.')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    /**
     * Calculate total cart value.
     */
    public function getTotalProperty(): float
    {
        return collect($this->cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
    }

    public function getTaxProperty(): float
    {
        return $this->cartTotal * 0.16; // Assuming 16% VAT or configurable rate
    }

    protected function recalculateTotals(): void
    {
        $this->cartTotal = $this->getTotalProperty();
        $this->cartTax = $this->getTaxProperty(); // adjust rate as needed
    }

    /**
     * Load product categories for current company.
     */
    protected function loadFloors(): void
    {
        $this->floorPlanOptions = FloorPlan::isCompany(current_company()->id)->get();
        $this->selectedPlanId = $this->floorPlanOptions->first()->id ?? null;
    }

    /**
     * Load product categories for current company.
     */
    protected function loadCategories(): void
    {
        $this->productCategoryOptions = ProductCategory::isCompany(current_company()->id)->get();
    }

    /**
     * Load products based on selected category or all products.
     */
    public function updatedSearchQuery($value): void
    {
        $this->loadProducts();
    }

    protected function loadProducts(): void
    {
        $query = Product::isCompany(current_company()->id);
        if ($this->selectedCategoryId) {
            $query->where('product_category_id', $this->selectedCategoryId);
        }
        if ($this->searchQuery) {
            $query->where('product_name', 'like', "%{$this->searchQuery}%");
        }
        $this->productOptions = $query->get();
    }

    protected function loadOrders(): void
    {
        $query = PosOrder::where('pos_id', $this->pos->id)
            ->where('company_id', current_company()->id);
        if ($this->orderStatusFilter) {
            $query->where('status', $this->orderStatusFilter);
        }
        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }
        $this->orders = $query->latest()->take(50)->get();
    }

    protected function loadCustomers(): void
    {
        $this->customers = Guest::where('company_id', current_company()->id)
            ->when($this->customerSearch, fn($query) => $query->where('name', 'like', "%{$this->customerSearch}%"))
            ->take(10)
            ->get();
    }

    public function createCustomer(): void
    {
        // Implement customer creation logic
        $this->dispatch('alert', type: 'success', message: 'Customer created.');
    }

    public function refundOrder($orderId): void
    {
        $order = PosOrder::find($orderId);
        if (!$order) {
            LivewireAlert::title('Order not found!')
                ->text('Order does not exist.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }
        if ($order->status === 'refunded') {
            LivewireAlert::title('Already refunded!')
                ->text('Order has already been refunded.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }
        $order->update(['status' => 'refunded']);
        LivewireAlert::title('Order refunded!')
            ->text('Order has been refunded successfully.')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    /**
     * Select calculator mode
     */
    public function selectCalculatorMode(string $mode): void
    {
        $this->calculatorMode = $mode;

        if ($this->selectedProductId && isset($this->cart[$this->selectedProductId])) {
            switch ($mode) {
                case 'qty':
                    $this->calculatorInput = $this->cart[$this->selectedProductId]['quantity'];
                    break;
                case 'price':
                    $this->calculatorInput = $this->cart[$this->selectedProductId]['unit_price'];
                    break;
                case 'discount':
                    $this->calculatorInput = $this->cart[$this->selectedProductId]['discount'];
                    break;
            }
        } else {
            $this->calculatorInput = '';
        }
    }

    public function applyCalculatorInput()
    {
        if (!$this->selectedProductId || !isset($this->cart[$this->selectedProductId])) {
            LivewireAlert::title('No product selected!')
                ->text('Please select a product.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }

        $value = $this->calculatorInput;
        if (!is_numeric($value) && $value !== '') {
            LivewireAlert::title('Invalid input!')
                ->text('The value entered is invalid.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }

        switch ($this->calculatorMode) {
            case 'qty':
                $quantity = (int) $value;
                if ($quantity < 1) {
                    $this->removeFromCart($this->selectedProductId); // Reuse removeFromCart to handle both cart and order detail deletion
                    $this->selectedProductId = null;
                    $this->calculatorInput = '';
                } else {
                    $product = Product::find($this->selectedProductId);
                    // if ($quantity > $product->quantity) {
                    //     LivewireAlert::title('Stock limit exceeded!')
                    //         ->text('Cannot set quantity beyond available stock.')
                    //         ->error()
                    //         ->position('top-end')
                    //         ->timer(4000)
                    //         ->toast()
                    //         ->show();
                    //     return;
                    // }
                    $this->cart[$this->selectedProductId]['quantity'] = $quantity;
                    $price = $this->cart[$this->selectedProductId]['unit_price'];
                    PosOrderDetail::where('pos_order_id', $this->order->id)
                        ->where('product_id', $this->selectedProductId)
                        ->update([
                            'quantity' => $quantity,
                            'sub_total' => $quantity * $price,
                            ]);
                }
                break;
            case 'price':
                $price = max(0, (float) $value);
                $this->cart[$this->selectedProductId]['unit_price'] = $price;
                $quantity = $this->cart[$this->selectedProductId]['quantity'];
                PosOrderDetail::where('pos_order_id', $this->order->id)
                    ->where('product_id', $this->selectedProductId)
                        ->update([
                            'unit_price' => $price,
                            'sub_total' => $quantity * $price,
                            ]);
                break;
            case 'discount':
                $discount = max(0, (float) $value);
                $maxDiscount = Auth::user()->hasPermissionTo('apply_high_discount') ? 100 : 50;
                if ($discount > $maxDiscount) {
                    LivewireAlert::title('Discount limit exceeded!')
                        ->text("Discount cannot exceed {$maxDiscount}%.")
                        ->error()
                        ->position('top-end')
                        ->timer(4000)
                        ->toast()
                        ->show();
                    return;
                }
                $this->cart[$this->selectedProductId]['discount'] = $discount;
                PosOrderDetail::where('pos_order_id', $this->order->id)
                    ->where('product_id', $this->selectedProductId)
                    ->update(['product_discount_amount' => $discount]);
                break;
        }

        $this->recalculateTotals();
        $this->order->update([
            'total_amount' => $this->cartTotal,
            // 'tax_amount' => $this->cartTax,
        ]);
        $this->saveCartToSession();
    }

    #[On('processPayment')]
    public function processPayment()
    {
        $this->dispatch('openModal', component: 'pos::modal.payment-modal', order: $this->order->id);
    }

    #[On('fallback-payment')]
    public function fallbackPayment($status){
        if (empty($this->cart) || !$this->order) {
            LivewireAlert::title('Cart is empty!')
                ->text('Please add items to the cart.')
                ->error()
                ->position('top-end')
                ->timer(4000)
                ->toast()
                ->show();
            return;
        }

        $this->order->update([
            'status' => 'receipt',
            'total_amount' => $this->cartTotal,
            // 'tax_amount' => $this->cartTax,
        ]);

        // Decrease product stock
        // foreach ($this->cart as $item) {
        //     $product = Product::find($item['id']);
        //     $product->decrement('quantity', $item['quantity']);
        // }

        $this->resetCart();
        $this->interface = 'payment';
        LivewireAlert::title('Order completed!')
            ->text('Order has been processed successfully.')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    #[On('switchInterface')]
    public function switchInterface($interface){
        $this->interface = $interface;
        if ($interface === 'tables') {
            $this->loadActiveOrder();
        }
        if ($interface === 'orders') {
            $this->loadOrders();
        }
    }

    public function unlock(): void
    {
        $this->isLocked = false;
        $this->dispatch('reset-inactivity-timer'); // Emit event to reset timer
        LivewireAlert::title('Unlocked!')
            ->text('POS is now active.')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    public function newOrder(): void
    {
        $this->resetCart();

        // if ($this->order) {
        //     $this->order->details()->delete();
        //     $this->order->delete();
        //     $this->order = null;
        // }

        $this->interface = 'tables';
        $this->selectedTable = null;
        $this->selectedCustomerId = null;
        LivewireAlert::title('New order started!')
            ->text('Ready to create a new order.')
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    #[On('assigned-guest')]
    public function assignGuest($guest): void
    {
        $this->selectedCustomerId = $guest;
        $this->guest = Guest::find($this->selectedCustomerId);
        if($this->order){
            $this->order->update([
                'customer_id' => $this->selectedCustomerId
            ]);
        }

        LivewireAlert::title('New guest selected!')
            ->text("{$this->guest->name} has been selected!")
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();
    }

    public function deleteOrder($orderId){
        $order = PosOrder::find($orderId);
        $receipt_number = $order->receipt_number;
        $order->details()->delete();
        $order->delete();

        LivewireAlert::title('Order has been deleted!')
            ->text("Order {$receipt_number} has been deleted")
            ->success()
            ->position('top-end')
            ->timer(4000)
            ->toast()
            ->show();

        $this->loadOrders();
    }

    /**
     * Load active order for the selected table or direct sale.
     */
    protected function loadActiveOrder(): void
    {
        $this->order = PosOrder::where('pos_id', $this->pos->id)
            ->where('company_id', current_company()->id)
            ->where('status', 'ongoing')
            ->when($this->selectedTable, fn($query) => $query->where('table_id', $this->selectedTable->id))
            ->when(!$this->selectedTable, fn($query) => $query->whereNull('table_id'))
            ->first();

        if ($this->order && !empty($this->cart)) {
            $this->syncCartWithOrder();
        }
    }

    /**
     * Sync cart with order details.
     */
    protected function syncCartWithOrder(): void
    {
        $this->cart = [];
        foreach ($this->order->details as $detail) {
            $this->cart[$detail->product_id] = [
                'id' => $detail->product_id,
                'name' => $detail->product->product_name,
                'unit_price' => $detail->unit_price,
                'quantity' => $detail->quantity,
                'discount' => $detail->product_discount_amount,
            ];
        }
        $this->recalculateTotals();
        $this->saveCartToSession();
    }

    /**
     * Create or update order when adding to cart.
     */
    protected function createOrder(): void
    {
        if (!$this->order) {
            $this->order = PosOrder::create([
                'pos_id' => $this->pos->id,
                'company_id' => current_company()->id,
                'cashier_id' => Auth::id(),
                'table_id' => $this->selectedTable?->id,
                'customer_id' => $this->selectedCustomerId,
                'total_amount' => $this->cartTotal,
                // 'tax_amount' => $this->cartTax,
                'status' => 'ongoing',
                'receipt_number' => 'ORD-' . uniqid(),
                // 'invoice_code' => strtoupper(Str::random(5)),
                'date' => now(),
                'service_type' => $this->selectedService['key'] ?? 'eat-in'
            ]);
            if ($this->selectedTable) {
                $this->selectedTable->update(['status' => 'occupied']);
            }
            $this->saveCartToSession();
        }
    }

    #[On('selectServiceType')]
    public function selectServiceType($service){
        $this->selectedService = $this->services[$service];
        if($this->order){
            $this->order->update([
                'service_type' => $this->selectedService['key']
            ]);
        }
    }

    public function render()
    {
        return view('pos::livewire.interface.home')
            ->extends('layouts.pos');
    }
}
