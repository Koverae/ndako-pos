<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('active_session_id')->nullable();
            $table->string('name');
            $table->boolean('has_multiple_employee')->default(false);
            $table->boolean('has_printer_connection')->default(false);
            $table->string('private_key');
            $table->enum('status', ['active', 'inactive', 'closed', 'on_break'])->default('inactive');
            $table->boolean('is_restaurant')->default(false);

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // POS Settings
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            // Is bar / restaurant
            $table->boolean('is_restaurant_bar')->default(false);
            // Mobile self-order & Kiosk
            $table->enum('self_ordering', ['disable', 'qr_menu', 'qr_menu_ordering', 'kiosk'])->default('qr_menu');
            // If self-order is qr_menu, qr_menu_ordering or kiosk
            $table->unsignedBigInteger('default_language_id')->nullable();
            $table->unsignedBigInteger('available_language_id')->nullable();
            // Payment
            $table->unsignedBigInteger('default_payment_method_id')->nullable();
            $table->boolean('has_automatically_validate_order')->default(true); //On terminal payment
            $table->boolean('has_maximum_difference_at_closing')->default(false); //On terminal payment
            $table->boolean('has_product_specific_closing_entry')->default(false); //On terminal payment
            $table->decimal('maximum_difference_at_closing', $precision = 12, $scale = 2)->default(0.00); //On terminal payment
            $table->boolean('has_tips')->default(false); //Accept customer tips or convert their change to a tip
            // Payment Terminal
            $table->boolean('has_stripe_payment_terminal')->default(false);
            $table->boolean('has_paytm_payment_terminal')->default(false);
            // POS Interface
            $table->boolean('has_start_category')->default(false);
            $table->unsignedBigInteger('start_category_id')->nullable();
            $table->boolean('has_restricted_categories')->default(false);
            $table->string('restricted_categories')->nullable();
            $table->boolean('has_large_scrollbar')->default(false);
            $table->boolean('has_margin_cost')->default(false);
            $table->boolean('show_product_images')->default(true);
            $table->boolean('show_category_images')->default(true);
            $table->boolean('has_share_orders')->default(false);
            // Accounting
            $table->unsignedBigInteger('default_sales_tax_id')->nullable();
            $table->unsignedBigInteger('defaulft_temporary_account_id')->nullable();
            $table->boolean('has_flexible_taxes')->default(false);
            $table->unsignedBigInteger('defaulft_fiscal_position_id')->nullable();
            $table->string('allowed_fiscal_positions')->nullable();
            $table->unsignedBigInteger('defaulft_order_journal_id')->nullable();
            $table->unsignedBigInteger('defaulft_invoice_journal_id')->nullable();
            // Sales
            $table->unsignedBigInteger('sales_team_id')->nullable();
            $table->unsignedBigInteger('down_payment_product_id')->nullable();
            // Pricing
            $table->boolean('has_price_control')->default(false);
            $table->boolean('has_line_discounts')->default(false);
            $table->boolean('has_sales_program')->default(false);
            $table->boolean('has_global_discount')->default(false);
            $table->boolean('has_pricer')->default(false);
            $table->enum('product_prices', ['tax-excluded', 'tax-included'])->default('tax-included');
            // Bills & Receipts
            $table->boolean('has_customer_header_footer')->default(false);
            $table->tinyText('custom_header')->nullable();
            $table->tinyText('custom_footer')->nullable();
            $table->boolean('has_automatic_receipt_printer')->default(false);
            $table->boolean('has_self_service_invoicing')->default(false);
            $table->enum('self_invoicing_print', ['qr-code', 'url', 'qr-code-url'])->default('qr-code');
            $table->boolean('has_qr_code_on_ticket')->default(false);
            $table->boolean('has_unique_code_on_ticket')->default(false); //Add a 6-digit code on the receipt to allow the user to request the invoice for an order on the portal.
            // Connected Devices
            $table->boolean('has_preparation_display')->default(false); //Display orders on the preparation display
            $table->string('internal_notes')->nullable(); //Display orders on the preparation display
            // Inventory
            $table->unsignedBigInteger('operation_type_id')->nullable(); //Used to record product pickings. Products are consumed from its default source location.
            $table->boolean('has_allow_ship_later')->default(false);
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->enum('shipping_policy', ['after_done', 'as_soon_as_possible'])->nullable();
            $table->unsignedBigInteger('barcode_nomenclature_id')->nullable()->comment('Use barcodes to scan products, customer cards, etc');

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // POS Payment Method
        Schema::create('pos_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->boolean('is_available_online')->default(false);
            $table->boolean('should_be_identified')->default(false);
            $table->enum('integration', ['none', 'terminal'])->default('none');

            $table->timestamps();
            $table->softDeletes();
        });

        // POS Sessions
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->string('unique_token')->unique()->nullable(); //Jeton unique de la session
            $table->unsignedBigInteger('open_by_id')->nullable();
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->decimal('starting_balance', $precision = 12, $scale = 2)->default(0.00);
            $table->decimal('closing_balance', $precision = 12, $scale = 2)->default(0.00);
            $table->enum('status', ['active', 'close_soon', 'closed', 'cancelled'])->default('active');

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // POS Orders
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('unique_token')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->unsignedBigInteger('pos_session_id')->nullable();
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('fiscal_position_id')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->integer('guest')->default(1);
            $table->enum('status', ['new', 'posted', 'invoiced', 'cancelled'])->default('new');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->string('payment_method')->default('cash');
            $table->string('receipt_number')->nullable();
            $table->integer('order_number')->nullable();
            $table->unsignedBigInteger('price_list_id')->nullable();
            $table->mediumText('note')->nullable();
            $table->decimal('total_amount', $precision = 12, $scale = 2)->default(0);
            $table->decimal('paid_amount', $precision = 12, $scale = 2)->default(0);
            $table->decimal('due_amount', $precision = 12, $scale = 2)->default(0);

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // POS Order Details
        Schema::create('pos_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_order_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('quantity', $precision = 12, $scale = 2);
            $table->decimal('price', $precision = 12, $scale = 2);
            $table->decimal('unit_price', $precision = 12, $scale = 2);
            $table->decimal('sub_total', $precision = 12, $scale = 2);
            $table->decimal('product_discount_amount', $precision = 12, $scale = 2);
            $table->string('product_discount_type')->default('fixed');
            $table->decimal('product_tax_amount', $precision = 12, $scale = 2);
            $table->mediumText('customer_note')->nullable();
            $table->decimal('refunded_quantity', $precision = 12, $scale = 2)->default(0.00);

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // POS Order Payments
        Schema::create('pos_order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('pos_session_id')->nullable();
            $table->unsignedBigInteger('pos_order_id');
            $table->date('date');
            $table->string('label')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->decimal('amount', $precision = 12, $scale = 2)->default(0);
            $table->decimal('due_amount', $precision = 12, $scale = 2)->default(0);

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Floor Plans
        Schema::create('floor_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->string('name');

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Tables
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->unsignedBigInteger('floor_plan_id')->nullable();
            $table->string('table_name');
            $table->integer('seats')->default(1);
            $table->enum('shape', ['square', 'circle', 'rectangle', 'hexagon', 'octagon'])->default('square');

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Product Categories
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('category_code')->nullable();
            $table->string('category_name');
            $table->unsignedBigInteger('force_removal_strategy_id')->nullable();
            $table->enum('costing_method', ['standard', 'fifo', 'avco']);
            $table->integer('available_start')->default(0);
            $table->integer('available_end')->default(24);
            $table->string('image_path')->nullable();

            // Accounting
            $table->unsignedBigInteger('income_account_id')->nullable();
            $table->unsignedBigInteger('expense_account_id')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->string('product_name');
            $table->string('product_reference')->nullable();
            $table->string('image_path')->nullable();

            $table->enum('product_type', ['storable', 'service', 'consumable', 'booking_fee']); //storable: Stock is managed, consumable: Stock isn't managed, service: non physical product
            $table->enum('invoicing_policy', ['ordered', 'delivered', 'prepaid'])->default('ordered'); //ordered: the products ordered by the customer, delivered: the products delivered to the customer

            $table->unsignedBigInteger('uom_id')->nullable();
            $table->unsignedBigInteger('purchase_uom_id')->nullable();
            $table->decimal('product_price', $precision = 12, $scale = 2)->default(0);
            $table->decimal('product_cost', $precision = 12, $scale = 2)->default(0);
            $table->string('product_order_tax')->nullable();
            $table->string('product_internal_reference')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_barcode_symbology')->nullable();
            $table->decimal('product_quantity', $precision = 10, $scale = 2)->default(0);
            $table->string('product_unit')->nullable();
            $table->integer('product_stock_alert')->default(0);
            $table->tinyInteger('product_tax_type')->nullable();
            $table->text('product_description')->nullable();
            $table->text('product_note')->nullable();
            $table->string('product_tag')->nullable();
            // Sale
            $table->string('optional_products')->nullable(); //Cross sale strategy, ex: for computers: waranty, software
            $table->text('sale_description')->nullable();
            // Purchase
            $table->string('suppliers')->nullable();
            $table->string('product_purchase_tax')->nullable();
            $table->enum('control_policy', ['ordered', 'received'])->default('received'); //Ordered: Control bills on ordered qties, received: Control bills on delivered qties
            $table->text('purchase_description')->nullable();
            // Taxes
            $table->text('sale_taxes')->nullable(); // Using text type to store serialized array
            $table->text('purchase_taxes')->nullable(); // Using text type to store serialized array
            $table->json('taxes')->nullable(); //['sale' => [], 'purchase' => [], 'misc' => []]
            // Tracking
            $table->enum('tracking', ['unique_serial_number', 'lots', 'no_tracking'])->default('no_tracking');
            //Logistics
            $table->unsignedBigInteger('responsible_id')->nullable(); //This user will be responsible of the product's purchase order
            $table->string('weight')->nullable();
            $table->string('volume')->nullable();
            // Operations
            $table->string('product_routes')->nullable();
            //Description
            $table->text('receipt_description')->nullable();
            // Accounting
            $table->unsignedBigInteger('income_account_id')->nullable();
            $table->unsignedBigInteger('expense_account_id')->nullable();
            $table->unsignedBigInteger('price_difference_account_id')->nullable();

            $table->boolean('can_be_sold')->default(true);
            $table->boolean('can_be_purchased')->default(true);
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Product Combos
        Schema::create('product_combos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('pos_id')->nullable();
            $table->string('name');
            $table->decimal('combo_price', $precision = 12, $scale = 2)->default(0);

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Product Combo Details
        Schema::create('product_combo_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('combo_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('extra_price', $precision = 12, $scale = 2)->default(0.00);
            $table->decimal('original_price', $precision = 12, $scale = 2)->default(0.00);

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pos_settings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pos_payment_methods', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pos_sessions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pos_order_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pos_order_payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('floor_plans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tables', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('product_combos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('product_combo_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
