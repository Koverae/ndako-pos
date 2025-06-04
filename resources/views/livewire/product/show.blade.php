@section('title', $product->name)

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.product-panel :product="$product" :event="'update-product'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.product-form :product="$product" />
</section>
<!-- Page Content -->
