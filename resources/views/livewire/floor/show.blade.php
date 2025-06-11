@section('title', $floor->name)

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.floor-panel :floor="$floor" :event="'update-floor'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.floor-form :floor="$floor" />
</section>
<!-- Page Content -->
