
    @section('title', $pos->name)
    @section('styles')
    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-bounce {
                    animation: bounce 2s infinite;
        }
        .bg-gradient-indigo-purple {
            background: linear-gradient(to right, #4f46e5, #6b21a8); /* Indigo 600 to Purple 700 */
        }
    </style>
    @endsection

    <main class="main relative" x-data="{ isLocked: @entangle('isLocked'), timer: null }" x-init="
    // Initialize inactivity timer
    let lastActivity = Date.now();
    const TIMEOUT = 20 * 60 * 1000; // 20 minutes in milliseconds

    const resetTimer = () => {
        lastActivity = Date.now();
        isLocked = false;
    };

    const checkInactivity = () => {
        if (Date.now() - lastActivity > TIMEOUT) {
            isLocked = true;
            $wire.set('isLocked', true);
        }
    };

    // Event listeners for activity
    ['mousemove', 'mousedown', 'keypress', 'touchstart'].forEach(event =>
        document.addEventListener(event, resetTimer)
    );

    // Start checking for inactivity
    timer = setInterval(checkInactivity, 1000);

    // Listen for reset event from Livewire
    window.Livewire.on('reset-inactivity-timer', resetTimer);
">
        <!-- Lock Screen -->
        <div x-show="isLocked" style="z-index: 99999;" class="fixed inset-0 flex items-center justify-center bg-body-secondary bg-opacity-75 backdrop-blur animate-fade-in">
            <div class="text-center p-6 rounded-lg shadow-2xl bg-white bg-opacity-10 backdrop-blur-lg max-w-md w-full mx-4">
                <img src="{{ asset('assets/images/logo/ndako.png') }}" alt="Ndako Logo" class="mx-auto mb-6 w-32 animate-pulse">
                <h2 class="text-3xl font-bold text-black mb-4">{{ __('POS Locked') }}</h2>
                <p class="text-lg text-gray-200 mb-6">{{ __('Click below to resume selling.') }}</p>
                <button wire:click="unlock" class="btn btn-primary text-white px-6 py-3 rounded-full font-semibold text-lg hover:bg-green-600 transition transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50 animate-bounce">
                    {{ __('Continue Selling') }}
                </button>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-md w-100 navbar-light d-block d-print-none k-sticky">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Logo -->
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="">
                        <img src="{{asset('assets/images/logo/ndako.png')}}" alt="Ndako Logo" class="navbar-brand-image">
                    </a>
                </h1>
                <!-- Logo End -->

                <!-- Navbar Buttons -->
                <div class="flex-row navbar-nav order-md-last">
                    <div class="d-md-flex d-flex">
                        <!-- Translate -->
                        <div class="nav-item dropdown d-md-flex me-3">
                            <a href="#" class="px-0 nav-link" data-bs-toggle="dropdown" id="dropdownMenuButton" title="Translate" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <i class="bi bi-translate" style="font-size: 16px;"></i>
                            </a>
                        </div>
                        <!-- Translate End -->

                        <!-- User's Avatar -->
                        <div class="nav-item dropdown">
                            <a href="#" class="p-0 nav-link d-flex lh-1 text-reset" data-bs-toggle="dropdown" aria-label="Open user menu">
                                <span class="avatar avatar-sm" style="background-image: url({{ Storage::url('avatars/' . auth()->user()->avatar) }})"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="https://docs.koverae.com/ndako" target="__blank" class="dropdown-item kover-navlink">Documentation</a>
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout')}}">
                                    @csrf
                                    <span  onclick="event.preventDefault(); this.closest('form').submit();" class="cursor-pointer kover-navlink dropdown-item">
                                        Log Out
                                    </span>
                                </form>
                                <!-- Authentication End -->
                            </div>
                        </div>
                        <!-- User's Avatar End -->
                    </div>
                </div>
                <!-- Navbar Buttons End -->

                <!-- Navbar Menu -->
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                        <ul class="navbar-nav">
                            <!-- Navbar Menu -->
                            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">

                                <li class="nav-item cursor-pointer" data-turbolinks>
                                    <a class="nav-link kover-navlink  {{ $interface == 'tables' ? 'selected' : '' }}" wire:click="switchInterface('tables')" style="margin-right: 5px;">
                                    <span class="nav-link-title">
                                        {{ __('Tables') }}
                                    </span>
                                    </a>
                                </li>

                                <li class="nav-item cursor-pointer" data-turbolinks>
                                    <a class="nav-link kover-navlink {{ $interface == 'register' ? 'selected' : '' }}" wire:click="switchInterface('register')" style="margin-right: 5px;">
                                    <span class="nav-link-title">
                                        {{ __('Register') }}
                                    </span>
                                    </a>
                                </li>

                                <li class="nav-item cursor-pointer" data-turbolinks>
                                    <a class="nav-link kover-navlink {{ $interface == 'orders' ? 'selected' : '' }}" wire:click="switchInterface('orders')" style="margin-right: 5px;">
                                    <span class="nav-link-title">
                                        {{ __('Orders') }}
                                    </span>
                                    </a>
                                </li>

                                @if($selectedTable)
                                <li class="nav-item" data-turbolinks>
                                    <span class="badge rounded-pill bg-info text-white fs-4 cursor-pointer fw-bolder text-truncate">
                                        {{ $selectedTable->table_name ?? __('Direct Sale') }}
                                    </span>
                                </li>
                                @endif

                            </div>
                            <!-- Navbar Menu -->
                        </ul>
                    </div>
                </div>
                <!-- Navbar Menu End -->

            </div>

        </nav>
        <!-- Regiter -->
        <div class="row {{ $interface == 'register' ? '' : 'd-none' }}">
            <!-- Product Section -->
            <section class="container-fluid {{ $tab == 'cart' ? 'd-none d-lg-block' : '' }} col-lg-7 col-md-12" style="height: 100vh;" id="product-box">
                <!-- Search Bar -->
                <div class="search-bar">
                    <input type="text" class="form-control" placeholder="Search products..." aria-label="Search products" wire:model.live="searchQuery">
                    <i class="bi bi-search search-icon"></i>
                </div>

                <!-- Categories -->
                <div class="category_section_buttons">
                    <div class="d-flex w-100">
                        <span class="category_button cursor-pointer home {{ $selectedCategoryId == null ? 'selected' : '' }}" wire:click="selectCategory('')">
                            <i class="bi bi-house-fill"></i>
                        </span>
                        <div class="cursor-pointer d-flex w-100 section_buttons">
                            @foreach ($productCategoryOptions as $category)
                            <span class="gap-2 category_button {{ $selectedCategoryId == $category->id ? 'selected' : '' }}" wire:click="selectCategory('{{ $category->id }}')">
                                {{ $category->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Product List -->
                <div class="gap-2 p-3 product-list row row-cols-2 row-cols-md-3 row-cols-lg-4">
                    @foreach ($productOptions as $product)
                    <article class="product cursor-pointer" wire:click="addToCart('{{ $product->id }}')">
                        <div class="product-information-tag">
                            <i class="bi bi-info" aria-label="Product info"></i>
                        </div>
                        <div class="badge badge-info"><i class="fas fa-infinity"></i></div>
                        <img src="{{ $product->image_path ? Storage::url('avatars/' . $product->image_path) . '?v=' . time() : asset('assets/images/default/product.png') }}"
                            alt="{{ $product->product_name }}" class="card-img-top" alt="Product">
                        <div class="product-content">
                            <div class="product-name">{{ $product->product_name }}</div>
                            <div class="price-tag">{{ format_currency($product->product_price) }}</div>
                        </div>
                    </article>
                    @endforeach
                </div>
                {{-- <div class="pagination">
                    {{ $productOptions->links() }}
                </div> --}}

            </section>

            <!-- Checkout Section -->
            <section class="col-lg-5 col-md-12 {{ $tab == 'pay' ? 'd-none d-lg-block' : '' }} " id="checkout-box">
                <div class="border-0 shadow-sm card">
                    <div class="card-body" id="cart-body">
                        <div class="overflow-y-auto order-container-bg-view flex-grow-1 d-flex flex-column text-start">

                            @forelse ($cart as $item)
                            <ul wire:click="selectProduct('{{ $item['id'] }}')">
                                <li class="p-2 cursor-pointer orderline lh-s  {{ $selectedProductId == $item['id'] ? 'selected' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="product-name w-75 fw-bolder pe-1 text-truncate">
                                            {{ $item['name'] }}
                                        </div>
                                        <div class="product-price w-25 text-end fw-bolder">
                                            {{ format_currency(($item['unit_price'] * $item['quantity']) ) }}
                                        </div>
                                    </div>
                                    <ul>
                                        <li class="price-per-unit">
                                            <em class="qty fst-normal fw-bolder me-1">{{ $item['quantity'] }}</em>
                                            unit(s) x {{ format_currency($item['unit_price']) }}
                                        </li>
                                        @if ($item['discount'] > 0)
                                        <li class="price-per-unit text-muted">
                                            {{ $item['discount'] }}% discount
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                            @empty
                            <div class="empty-cart d-flex flex-column align-items-center justify-content-center h-100 w-100 text-muted">
                                <i class="bi bi-cart-fill rotate-45" style="font-size: 60px; color: #898989;"></i>
                                <br>
                                <h3>
                                    {{ __('No items in cart.') }}
                                </h3>
                            </div>
                            @endforelse
                        </div>
                        <div class="px-3 py-2 order-summary w-100 bg-100 text-end fw-bolder fs-2 lh-sm">
                            Total: <span class="total">{{ format_currency($cartTotal) }}</span>
                            <div class="text-muted subentry">
                                Taxes: <span class="tax">(+) {{ format_currency($cartTax) }}</span>
                            </div>
                        </div>
                        <div class="flex-wrap control_buttons d-flex bg-300 border-bottom">

                            <button class="gap-2 k_price_list_button btn btn-light rounded-0 fw-bolder">
                                <i class="fas fa-tags"></i> <span>Pricelists</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder">
                                <i class="fas fa-sync-alt"></i> <span>Refund</span>
                            </button>
                            <button onclick="Livewire.dispatch('openModal', {component: 'pos::modal.service-type-modal'})" class="gap-2 btn btn-light rounded-0 fw-bolder preset">
                                @if($selectedService)
                                <i class="{{ $selectedService['icon'] }}"></i> <span>{{ $selectedService['label'] }}</span>
                                @else
                                {{ __('Service Type') }}
                                @endif
                            </button>

                            <button class="gap-3 btn btn-light rounded-0 fw-bolder" wire:click="switchInterface('tables')" style="background-color: #B7EDBE;">
                                <i class="fas fa-chair"></i> <span>{{ $selectedTable->table_name ?? __('Table') }}</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder">
                                <i class="bi bi-stickies"></i> <span>Customer Note</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder">
                                <i class="bi bi-stickies"></i> <span>Note</span>
                            </button>

                            <button wire:click="cancelOrder" wire:confirm="{{ __('Are you sure to reset the cart?') }}" class="gap-2 btn btn-light rounded-0 fw-bolder {{ empty($cart) ? 'disabled' : '' }}" id="reset-cart">
                                <i class="fas fa-trash"></i> <span>Cancel Order</span>
                            </button>
                            @php
                                $customer = $this->guest ? Str::limit($this->guest->name, 10) : __('Guest');
                            @endphp
                            <button onclick="Livewire.dispatch('openModal', {component: 'channelmanager::modal.guest-modal'})" class="gap-2 btn btn-light rounded-0 fw-bolder" id="reset-cart">
                                <i class="fas fa-user"></i> <span>{{ $customer }}</span>
                            </button>

                        </div>

                        <!-- Calculator -->
                        <div class="flex-wrap calculator_buttons d-flex bg-300 border-bottom">
                            <div class="flex-wrap w-25 d-flex" id="vertical_buttons">
                                <button onclick="Livewire.dispatch('openModal', {component: 'pos::modal.payment-modal', arguments: { order: {{ $order->id ?? null }} } })" class="btn btn-light rounded-0 fw-bolder {{ empty($cart) ? 'disabled' : '' }}" id="pay">
                                    {{ __('Payment') }}
                                </button>
                            </div>
                            <div x-data="calculatorComponent(@this)"
                                x-init="
                                    window.addEventListener('keydown', (e) => {
                                        press(e.key);
                                    });"
                                class="flex-wrap w-75 d-flex"
                            >
                                <template x-for="key in keys" :key="key.label + key.value">
                                    <button
                                        type="button"
                                        @click="press(key.value)"
                                        :class="[
                                            'btn',
                                            'rounded-0',
                                            'fw-bolder',
                                            key.class,
                                            key.mode && $wire.calculatorMode === key.value ? 'selected' : ''
                                        ]"
                                        :style="key.style"
                                    >
                                        <template x-if="key.icon">
                                            <i :class="key.icon"></i>
                                        </template>
                                        <template x-if="!key.icon">
                                            <span x-text="key.label"></span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <!-- Calculator -->
                    </div>
                </div>
            </section>

            <!-- Mobile Checkout -->
            <section class="d-lg-none" id="mobile-checkout-box">
                <div class="fixed-bar">
                    <button wire:click="changeTab('pay')" class="text-white btn-switch_pane rounded-0 fw-bolder review-button" id="pay-order">
                        <span class="fs-1 d-block">Pay</span>
                        <span>{{ format_currency($cartTotal) }}</span>
                    </button>
                    <button wire:click="changeTab('cart')" class="text-black btn-switch_pane rounded-0 fw-bolder review-button">
                        <span class="fs-1 d-block">Cart</span>
                        <span>{{ count($cart) }} items</span>
                    </button>
                </div>
            </section>
        </div>
        <!-- Regiter -->

        <!-- Payment -->
        <div class="payment-container bg-white {{ $interface == 'payment' ? '' : 'd-none' }}" style="height: 100vh;">
            <div class="payment-confirmed">
                <div class="row">
                    <div class="top-content d-print-none">
                        <h1>{{ format_currency($order->total_amount ?? 0) }}</h1>
                    </div>

                    <!-- Actions -->
                    <div class="col-md-6 d-print-none">
                        <div class="actions justify-content-between flex-lg-grow-1">

                            <div class="payment-success-card m-1 mt-2 d-flex flex-column align-items-center mb-3 p-3 g-3 border-success rounded bg-success-subtle text-success fs-3">
                                {{-- <i class="fas fa-check"></i> --}}
                                <i class="bi bi-check-circle mb-2" style="font-size: 35px;"></i>
                                <span style="font-weight: 900;" class="fs-2 ">{{ __('Payment Successful') }}</span>
                                <div class="d-flex mt-2 justify-content-center align-items-center gap-2 fw-bolder">
                                    <span>{{ format_currency(7490) }}</span>
                                    <span class="edit-order-payment badge bg-success text-white rounded pt-1">
                                        {{ __('Edit Payment') }}
                                    </span>
                                </div>
                            </div>

                            <button class="button m-1 btn btn-print btn-lg py-5 gap-2 w-100" onclick="window.print();">
                                <i class="mr-1 bi bi-printer fw-bold"></i>
                                <span>{{ __('Print Full Receipt') }}</span>
                            </button>

                            <div class="gap-1 mt-3 validation_buttons d-print-none d-none d-lg-flex w-100">
                                <a wire:click="newOrder" class="text-center cursor-pointer text-white p-3 rounded m-1 btn-switch_pane btn-primary fw-bolder review-button w-50 text-decoration-none">
                                    <span class="fs-1 d-block">{{ __('New Order') }}</span>
                                </a>
                                <button wire:click="switchInterface('orders')" class="text-white p-3 rounded m-1 btn-switch_pane btn-primary fw-bolder review-button w-50">
                                    <span class="mb-1 fs-1 d-block">{{ __('Orders') }}</span>
                                </button>
                            </div>

                            <!-- Mobile View -->
                            <div class="gap-1 mt-3 validation_buttons d-print-none d-flex d-lg-none fixed-bottom w-100">
                                <a wire:click="newOrder" class="text-center cursor-pointer text-white p-3 rounded m-1 btn-switch_pane btn-primary fw-bolder review-button w-50 text-decoration-none">
                                    <span class="fs-1 d-block">{{ __('New Order') }}</span>
                                </a>
                                <button wire:click="switchInterface('orders')" class="text-white p-3 rounded m-1 btn-switch_pane btn-primary fw-bolder review-button w-50">
                                    <span class="mb-1 fs-1 d-block">{{ __('Orders') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt -->
                    <div class="overflow-hidden text-center pos-receipt-container col-md-6 d-none d-md-flex flex-grow-1 flex-lg-grow-0 user-select-none justify-content-center bg-200">
                        <div class="p-3 m-3 overflow-y-auto bg-white border rounded receipt-block d-inline-block w-50 bg-view text-start">
                            <div class="p-2 pos-receipt">
                                <!-- Logo -->
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <img src="{{ asset('assets/images/logo/ndako.png') }}" alt="Ndako Logo" class="pos-receipt-logo">
                                </div>

                                <!-- Company Info -->
                                <div class="d-flex flex-column align-items-center company-info">
                                    <span>{{ current_company()->address }}</span>
                                    @if(current_company()->phone)
                                    <span>Tel: {{ current_company()->phone }}</span>
                                    @endif
                                    <div>-------------------------</div>
                                    <div>{{ __('Guest') }}: {{ $order->guest->name ?? 'Unknown' }}</div>
                                    <div>Served by: {{ $order->cashier->name ?? 'Unknown' }}</div>
                                    <div class="receipt-number"><span class="fs-3">GHJKSSHSJJKJS</span></div>
                                </div>

                                <!-- Order list -->
                                <div class="overflow-y-auto mt-2 order-container-bg-view flex-grow-1 d-flex flex-column text-start">
                                    <ul>
                                        @if ($order)
                                            @forelse ($order->details as $item)
                                                <li class="p-2 cursor-pointer orderline lh-sm">
                                                    <div class="d-flex">
                                                        <div class="w-75 d-flex gap-2 pe-1 text-truncate">
                                                            <span class="qty fw-bolder">{{ $item->quantity }}</span>
                                                            <span class="name">{{ $item->product->product_name ?? 'Unknown' }}</span>
                                                        </div>
                                                        <div class="product-price w-50 text-end">
                                                            {{ format_currency(($item->unit_price * $item->quantity) * (1 - $item->product_discount_amount / 100)) }}
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="p-2 text-muted">{{ __('No items in order.') }}</li>
                                            @endforelse
                                        @else
                                            <li class="p-2 text-muted">{{ __('No active order.') }}</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Separator -->
                                <div class="align-items-center">---------------------------</div>

                                <!-- Totals -->
                                <div class="overflow-y-auto order-container-bg-view flex-grow-1 d-flex flex-column text-start">
                                    <ul>
                                        <li class="p-2 cursor-pointer orderline lh-sm">
                                            <div class="d-flex">
                                                <div class="w-75 pe-1 text-truncate">{{ __('Subtotal') }}</div>
                                                <div class="w-50 text-end">{{ format_currency($order->total_amount ?? 0) }}</div>
                                            </div>
                                        </li>
                                        <li class="p-2 cursor-pointer orderline lh-sm">
                                            <div class="d-flex">
                                                <div class="w-75 pe-1 text-truncate">{{ __('VAT') }} {{ config('pos.tax_rate', 0.16) * 100 }}%</div>
                                                <div class="w-50 text-end">{{ format_currency($cartTax) }}</div>
                                            </div>
                                        </li>
                                        <li class="p-2 cursor-pointer orderline lh-sm">
                                            <div class="d-flex">
                                                <div class="w-75 pe-1 text-truncate fw-bold">{{ __('Total') }}</div>
                                                <div class="w-50 text-end fw-bold">{{ format_currency($order->total_amount ?? 0 + $cartTax) }}</div>
                                            </div>
                                        </li>
                                        <li class="p-2 cursor-pointer orderline lh-sm">
                                            <div class="d-flex">
                                                <div class="w-75 pe-1 text-truncate">{{ __('Payment') }}</div>
                                                <div class="w-50 text-end">{{ format_currency($order->total_amount ?? 0 + $cartTax) }}</div>
                                            </div>
                                            <ul>
                                                <!-- Placeholder for payment methods; extend as needed -->
                                                <li class="price-per-unit mt-1" style="padding-left: 3px;">Cash: {{ format_currency($order->total_amount ?? 0 + $cartTax) }}</li>
                                                <li class="price-per-unit mt-1" style="padding-left: 3px;">Card: {{ format_currency(0) }}</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Qr Code -->
                                <div class="pos-receipt-order-data d-flex mt-2 mb-2 text-center fs-5">
                                    <img src="{{ asset('assets/images/default/sample-qrcode.png') }}" style="height: 100px; width: 100px;" alt="" class="">

                                    <div class="d-block">
                                        <span class="fw-bolder">
                                            {{ __('Need an invoice?') }}
                                        </span>
                                        <p>Code: yhK2r</p>
                                    </div>
                                </div>

                                <!-- Order Meta -->
                                <div class="pos-receipt-order-data d-flex mt-2 text-center fs-5 flex-column align-items-center">
                                    <p>{{ __('Powered by ') }} <a href="https://ndako.koverae.com" target="_blank" class="fw-bold">Ndako</a></p>
                                    <div>{{ \Carbon\Carbon::parse($order->date ?? now())->format('d-m-y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Payment -->

        <!-- Tables -->
        <div class="table-container bg-white {{ $interface == 'tables' ? '' : 'd-none' }}" style="height: 100vh;">
                <!-- Control Panel -->
                <div class="gap-3 px-3 table-navbar d-flex flex-column gap-lg-1 d-print-none">
                    <div class="gap-5 table-navbar-main p-2 d-flex flex-nowrap justify-content-between align-items-lg-start flex-grow-1">
                        <!-- Breadcrumbs -->
                        <div class="gap-1 table-navbar-left d-flex align-items-center order-0">
                            <button wire:click="newOrder" class="new-order btn btn-primary fs-3 btn-lg lh-lg">
                                <i class="bi bi-plus fs-3"></i> <span class="d-none d-lg-flex">New Order</span>
                            </button>
                        </div>

                        <div id="actions" class="order-2 gap-2 d-inline-flex rounded-2 table-navbar-actions d-flex align-items-center justify-content-between order-lg-1 ">

                            <div class="gap-3 d-flex align-items-center">
                                <div class="table-navbar-buttons align-items-center">

                                    @foreach ($floorPlanOptions as $plan)
                                    <span wire:click="changeFloorPlan('{{ $plan->id }}')" class="w-auto gap-1 k_switch_view fs-3 d-lg-inline-block btn btn-secondary {{ $plan->id == $selectedPlanId ? 'active' : '' }} k-list">
                                        {{ $plan->name }}
                                    </span>
                                    @endforeach

                                    <!-- Action Buttons -->

                                </div>
                            </div>
                        </div>

                        <div class="flex-wrap order-3 align-items-end table-navbar-left d-flex flex-md-wrap align-items-center justify-content-end gap-l-1 gap-xl-5 order-lg-2 flex-grow-1">
                            <!-- Display panel buttons -->
                            <div class="table-navbar-buttons d-print-none d-xl-inline-flex btn-group">
                                <!-- Button view -->
                                    {{-- <span class="w-auto gap-1 k_switch_view fs-3 d-lg-inline-block btn btn-secondary k-list">
                                        {{ __('Main Floor') }}
                                    </span> --}}

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-section row overflow-y-auto p-5 h-100">

                    @foreach($floorPlanOptions->where('id', $selectedPlanId)->first()->tables as $table)
                    <div class="floor col-md-3">
                        <div class="floor-table p-0 rounded flex-column cursor-pointer justify-content-between position-absolute">
                            <div wire:click="selectTable('{{ $table->id }}')" class="info {{ $selectedTable?->id == $table->id ? 'active' : '' }} w-100 h-100 overflow-hidden ">
                                <div class="label top-50 start-50 fw-bolder position-absolute fs-3 translate-middle">
                                    {{ $table->table_name }}
                                    <br>
                                    <small>{{ inverseSlug($table->status) }}</small>
                                </div>
                            </div>
                            @if($table->status == 'occupied')
                            <button wire:click="releaseTable('{{ $table->id }}')" class="btn btn-danger btn-sm position-absolute bottom-0 end-0 m-1">
                                Release
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach

                </div>
        </div>
        <!-- Tables -->

        <!-- Orders -->

        <!-- Orders -->
        <div class="order-container overflow-y-auto bg-white {{ $interface == 'orders' ? '' : 'd-none' }}" style="height: 100vh;">
            <div class="p-4">
                <h2 class="text-2xl font-bold mb-4">{{ __('Order History') }}</h2>

                <!-- Filters -->
                <div class="flex flex-col md:flex-row gap-4 mb-4">
                    <div class="w-full md:w-1/3">
                        <label class="text-sm font-medium text-gray-600">{{ __('Status') }}</label>
                        <select wire:model="orderStatusFilter" class="w-full mt-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All') }}</option>
                            <option value="ongoing">{{ __('Ongoing') }}</option>
                            <option value="completed">{{ __('Completed') }}</option>
                            <option value="refunded">{{ __('Refunded') }}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="text-sm font-medium text-gray-600">{{ __('Payment Status') }}</label>
                        <select wire:model="paymentStatusFilter" class="w-full mt-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('All') }}</option>
                            <option value="unpaid">{{ __('Unpaid') }}</option>
                            <option value="paid">{{ __('Paid') }}</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto ">
                    <table class="w-100 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Order ID') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Table') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payment') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">{{ $order->receipt_number }}</td>
                                <td class="px-4 py-3 text-sm">{{ $order->table->table_name ?? 'Direct Sale' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $order->guest->name ?? 'No Guest' }}</td>
                                <td class="px-4 py-3 text-sm">{{ format_currency($order->total_amount + ($order->tax_amount ?? 0)) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm flex gap-2">
                                    @php
                                        $cartData = session("pos_cart_{$pos->id}");
                                    @endphp

                                    @if ($order->status === 'ongoing' && ($cartData['active_order_id'] ?? null) != $order->id)
                                        <button wire:click="selectOrder('{{ $order->id }}')" class="btn btn-primary btn-sm">{{ __('Select') }}</button>
                                    @endif
                                    @if($order->status == 'ongoing')
                                    <button wire:click="deleteOrder('{{ $order->id }}')" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                    @endif
                                    @if($order->status != 'refunded')
                                    <button wire:click="refundOrder('{{ $order->id }}')" class="btn btn-danger btn-sm">{{ __('Refund') }}</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-sm text-gray-500 text-center">{{ __('No orders found.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Orders -->
    </main>

<script>
function calculatorComponent($wire) {
    return {
        input: '',
        keys: [
            { label: '1', value: '1' },
            { label: '2', value: '2' },
            { label: '3', value: '3' },
            { label: 'Qty', value: 'qty', class: 'btn-light', mode: true },
            { label: '4', value: '4' },
            { label: '5', value: '5' },
            { label: '6', value: '6' },
            { label: 'Disc', value: 'discount', icon: 'bi bi-percent', class: 'btn-light', mode: true },
            { label: '7', value: '7' },
            { label: '8', value: '8' },
            { label: '9', value: '9' },
            { label: 'Price', value: 'price', class: 'btn-light', mode: true },
            { label: '÷', value: '/', style: 'background-color: #F5D976;' },
            { label: '0', value: '0' },
            { label: '.', value: '.', style: 'background-color: #F5D7CB;' },
            { label: '', value: 'Backspace', icon: 'bi bi-backspace', style: 'background-color: #FAA0A0;' },
        ],

        press(value) {

            // Prevent any action if no product is selected
            if (!$wire.selectedProductId) {
                return;
            }

            if (['qty', 'discount', 'price'].includes(value)) {
                $wire.selectCalculatorMode(value); // Now $wire is defined
                return;
            }

            // Handle mapped keys
            switch (value) {
                case 'q':
                    $wire.selectCalculatorMode('qty');
                    return;
                case 'p':
                    $wire.selectCalculatorMode('price');
                    return;
                case 'd':
                    $wire.selectCalculatorMode('discount');
                    return;
                case '/':
                    this.input += '/';
                    break;
                case 'Backspace':
                    this.input = this.input.slice(0, -1);
                    break;
                case 'Enter':
                    // Placeholder for calculation or submission logic
                    console.log('Enter pressed');
                    break;
                default:
                    if (/^[0-9]$/.test(value) || value === '.') {
                        this.input += value;
                    } else {
                        return; // Ignore unknown keys
                    }
            }

            // Optional: send to Livewire if needed
            $wire.set('calculatorInput', this.input);
            $wire.applyCalculatorInput(); // ← Realtime update on each key press

        },

    };
}
</script>

