@section('title', __('New Floor Plan'))

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.floor-panel :event="'create-floor'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.floor-form />
</section>
<!-- Page Content -->
