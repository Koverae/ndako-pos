@section('title', __('New Product'))

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.product-panel :event="'create-product'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.product-form />
</section>
<!-- Page Content -->
