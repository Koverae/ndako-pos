@section('title', "Floor Plans")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.floor-panel :isForm="false" />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.floor-table />
</section>
<!-- Page Content -->
