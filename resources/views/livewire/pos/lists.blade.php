@section('title', "Point Of Sales")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.pos-panel />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.pos-table />
</section>
<!-- Page Content -->
