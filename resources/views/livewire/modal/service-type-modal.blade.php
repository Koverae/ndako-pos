<div>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Select Service Type') }}</h5>
            <span class="btn-close" wire:click="$dispatch('closeModal')"></span>
        </div>

        <div class="modal-body">
            <div class="container-fluid mt-3">
                <div class="row g-3 justify-content-center">

                    @foreach($services as $key => $service)
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card text-center border shadow-sm h-100 cursor-pointer hover-shadow-sm"
                                 wire:click="selectService('{{ $key }}')">
                                <div class="card-body py-4">
                                    <div class="mb-2 fs-2 " style="color: #04464A">
                                        <i class="{{ $service['icon'] }}"></i>
                                    </div>
                                    <h5 class="card-title mb-0">{{ __($service['label']) }}</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" wire:click="$dispatch('closeModal')">{{ __('Discard') }}</button>
        </div>
    </div>
</div>
