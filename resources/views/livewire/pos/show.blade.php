@section('title', $pos->name)

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.pos-panel :pos="$pos" :event="'update-pos'" :isForm="true" />
@endsection
<!-- Page Content -->
<section class="">
    <livewire:pos::form.pos-form :pos="$pos" />
</section>
<!-- Page Content -->
