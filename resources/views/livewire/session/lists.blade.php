@section('title', "Restaurant Sessions")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.session-panel :isForm="false" />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.session-table />
</section>
<!-- Page Content -->
