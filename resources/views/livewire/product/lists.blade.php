@section('title', "Product Categories")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.product-panel :isForm="false" />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.product-table />
</section>
<!-- Page Content -->
