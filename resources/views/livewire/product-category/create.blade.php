@section('title', __('New Category'))

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.product-category-panel :event="'create-category'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.product-category-form />
</section>
<!-- Page Content -->
