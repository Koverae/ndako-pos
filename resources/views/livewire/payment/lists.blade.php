@section('title', "Restaurant Payments")

<!-- Control Panel -->
@section('control-panel')
<livewire:pos::navbar.control-panel.payment-panel :isForm="false" />
@endsection
<!-- Page Content -->
<section class="w-100">
    <livewire:pos::table.payment-table />
</section>
<!-- Page Content -->
