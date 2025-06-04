@section('title', $category->name)

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.product-category-panel :category="$category" :event="'update-category'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.product-category-form :category="$category" />
</section>
<!-- Page Content -->
