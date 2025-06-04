
    @section('title', $pos->name)
    <main class="main">
        <div class="row">
            <!-- Product Section -->
            <section class="container-fluid {{ $tab == 'cart' ? 'd-none d-lg-block' : '' }} col-lg-7 col-md-12" style="height: 100vh;" id="product-box">
                <!-- Search Bar -->
                <div class="search-bar">
                    <input type="text" class="form-control" placeholder="Search products..." aria-label="Search products">
                    <i class="bi bi-search search-icon"></i>
                </div>

                <!-- Categories -->
                <div class="category_section_buttons">
                    <div class="d-flex w-100">
                        <span class="category_button cursor-pointer home {{ $selectedCategoryId == null ? 'selected' : '' }}" wire:click="selectCategory('')">
                            <i class="bi bi-house-fill"></i>
                        </span>
                        <div class="cursor-pointer d-flex w-100 section_buttons">
                            <span class="gap-2 category_button">
                                <span>{{ __('Drinks') }}</span>
                            </span>
                            <span class="gap-2 category_button">
                                Food
                            </span>
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
                    <article class="product">
                        <div class="product-information-tag">
                            <i class="bi bi-info" aria-label="Product info"></i>
                        </div>
                        <div class="badge badge-info">{{ $product->product_quantity }}</div>
                        <img src="{{ $product->image_path ? Storage::url('avatars/' . $product->image_path) . '?v=' . time() : asset('assets/images/default/product.png') }}" alt="{{ $product->product_name }}" class="card-img-top" alt="Deluxe Suite">
                        <div class="product-content">
                            <div class="product-name">{{ $product->product_name }}</div>
                            <div class="price-tag">{{ format_currency(($product->product_price)) }}</div>
                        </div>
                    </article>
                    @endforeach
                    
                    <article class="product">
                        <div class="product-information-tag">
                            <i class="bi bi-info" aria-label="Product info"></i>
                        </div>
                        <div class="badge badge-info">20</div>
                        <img src="{{ asset('assets/images/default/cappucino.jpg')}}" class="card-img-top" alt="Deluxe Suite">
                        <div class="product-content">
                            <div class="product-name">Cappucino</div>
                            <div class="price-tag">KSh 420</div>
                        </div>
                    </article>

                    <article class="product">
                        <div class="product-information-tag">
                            <i class="bi bi-info" aria-label="Product info"></i>
                        </div>
                        <div class="badge badge-info">20</div>
                        <img src="{{ asset('assets/images/default/bubble-tea.jpg')}}" class="card-img-top" alt="Deluxe Suite">
                        <div class="product-content">
                            <div class="product-name">Bubble Tea</div>
                            <div class="price-tag">KSh 620</div>
                        </div>
                    </article>
                    

                </div>
            </section>

            <!-- Checkout Section -->
            <section class="col-lg-5 col-md-12 {{ $tab == 'pay' ? 'd-none d-lg-block' : '' }} " id="checkout-box">
                <div class="border-0 shadow-sm card">
                    <div class="card-body" id="cart-body">
                        <div class="overflow-y-auto order-container-bg-view flex-grow-1 d-flex flex-column text-start">
                            <ul>
                                <li class="p-2 cursor-pointer orderline lh-sm selected">
                                    <div class="d-flex">
                                        <div class="product-name w-75 d-inline-block flex-grow-1 fw-bolder pe-1 text-truncate">
                                            <span class="text-wrap">Cheese Burger</span>
                                        </div>
                                        <div class="product-price w-25 d-inline-block text-end price fw-bolder">
                                            KSh 25,000
                                        </div>
                                    </div>
                                    <ul>
                                        <li class="price-per-unit">
                                            <em class="qty fst-normal fw-bolder me-1">2 </em> units x KSh 10,000
                                        </li>
                                        <li class="price-per-unit text-muted">
                                            15% discount
                                        </li>
                                    </ul>
                                </li>
                                <li class="p-2 cursor-pointer orderline lh-sm">
                                    <div class="d-flex">
                                        <div class="product-name w-75 d-inline-block flex-grow-1 fw-bolder pe-1 text-truncate">
                                            <span class="text-wrap">Chapati</span>
                                        </div>
                                        <div class="product-price w-25 d-inline-block text-end price fw-bolder">
                                            KSh 15.00
                                        </div>
                                    </div>
                                    <ul>
                                        <li class="price-per-unit">
                                            <em class="qty fst-normal fw-bolder me-1">5 </em> units x KSh 75.00
                                        </li>
                                        <li class="price-per-unit text-muted">
                                            15% discount
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="px-3 py-2 order-summary w-100 bg-100 text-end fw-bolder fs-2 lh-sm">
                            Total: <span class="total">KSh 20,000</span>
                            <div class="text-muted subentry">
                                Taxes: <span class="tax">(+) KSh 540</span>
                            </div>
                        </div>
                        <div class="flex-wrap control_buttons d-flex bg-300 border-bottom">

                            <button class="gap-2 k_price_list_button btn btn-light rounded-0 fw-bolder">
                                <i class="fas fa-tags"></i> <span>Pricelists</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder">
                                <i class="fas fa-sync-alt"></i> <span>Refund</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder preset">
                                <i class="fas fa-utensils"></i> <span>Eat In</span>
                            </button>

                            <button class="gap-3 btn btn-light rounded-0 fw-bolder" disabled style="background-color: #B7EDBE;">
                                <i class="fas fa-chair"></i> <span>T1</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder">
                                <i class="bi bi-stickies"></i> <span>Customer Note</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder">
                                <i class="bi bi-stickies"></i> <span>Note</span>
                            </button>

                            <button class="gap-2 btn btn-light rounded-0 fw-bolder" id="reset-cart">
                                <i class="fas fa-trash"></i> <span>Cancel Order</span>
                            </button>
                            <button class="gap-2 btn btn-light rounded-0 fw-bolder" id="reset-cart">
                                <i class="fas fa-user"></i> <span>Guest</span>
                            </button>

                        </div>
                        <div class="flex-wrap calculator_buttons d-flex bg-300 border-bottom">
                            <div class="flex-wrap w-25 d-flex" id="vertical_buttons">
                                <button class="btn btn-light rounded-0 fw-bolder" id="pay">
                                    Pay
                                </button>
                            </div>
                            <div class="flex-wrap w-75 d-flex">
                                <button class="k_price_list_button btn btn-light rounded-0 fw-bolder">
                                    1
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    2
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    3
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder selected">
                                    Qty
                                </button>
                                <button class="k_price_list_button btn btn-light rounded-0 fw-bolder">
                                    4
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    5
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    6
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    <i class="bi bi-percent"></i> Disc
                                </button>
                                <button class="k_price_list_button btn btn-light rounded-0 fw-bolder">
                                    7
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    8
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    9
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    Price
                                </button>
                                <button class="k_price_list_button btn btn-light rounded-0 fw-bolder" style="background-color: #F5D976;">
                                    ÷
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder">
                                    0
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder" style="background-color: #F5D7CB;">
                                    .
                                </button>
                                <button class="btn btn-light rounded-0 fw-bolder" style="background-color: #FAA0A0;">
                                    <i class="bi bi-backspace"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Mobile Checkout -->
            <section class="d-lg-none" id="mobile-checkout-box">
                <div class="fixed-bar">
                    <button wire:click="changeTab('pay')" class="text-white btn-switch_pane rounded-0 fw-bolder review-button" id="pay-order">
                        <span class="fs-1 d-block">Pay</span>
                        <span>KSh 20,000</span>
                    </button>
                    <button wire:click="changeTab('cart')" class="text-black btn-switch_pane rounded-0 fw-bolder review-button">
                        <span class="fs-1 d-block">Cart</span>
                        <span>2 items</span>
                    </button>
                </div>
            </section>
        </div>
    </main>
