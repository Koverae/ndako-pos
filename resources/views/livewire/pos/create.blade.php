@section('title', "New")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.pos-panel :event="'create-pos'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.pos-form />
</section>
<!-- Page Content -->
