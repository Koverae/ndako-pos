@section('title', "Orders")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.order-panel :isForm="false" />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.order-table />
</section>
<!-- Page Content -->
