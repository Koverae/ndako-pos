@section('title', "Product Categories")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.product-category-panel :isForm="false" />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.product-category-table />
</section>
<!-- Page Content -->
