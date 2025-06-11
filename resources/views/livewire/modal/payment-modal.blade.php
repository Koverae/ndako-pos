<div>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ __("Make Payment for") }} #{{ $order->receipt_number }}</h5>
            <span class="btn-close" wire:click="$dispatch('closeModal')"></span>
        </div>

            <div class="modal-body pt-0">
                <!-- Tabs -->
                <!-- Card-style Tabs -->
                <div class="d-flex gap-3 mb-4 justify-content-center">
                    <div class="card cursor-pointer {{ $tab === 'offline' ? 'border-primary shadow-sm' : 'border-light' }}"
                         wire:click="$set('tab', 'offline')" style="width: 45%; cursor: pointer;">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-cash-coin fs-2 mb-2 text-primary"></i>
                            <h6 class="mb-0">Offline</h6>
                            <small class="text-muted">Cash, Card, M-Pesa</small>
                        </div>
                    </div>

                    <div class="card cursor-pointer {{ $tab === 'online' ? 'border-success shadow-sm' : 'border-light' }}"
                         wire:click="$set('tab', 'online')" style="width: 45%; cursor: pointer;">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-credit-card-2-front fs-2 mb-2 text-success"></i>
                            <h6 class="mb-0">Online</h6>
                            <small class="text-muted">Paystack, Card, M-Pesa, Bank</small>
                        </div>
                    </div>
                </div>

                <!-- Offline Payment -->
                @if($tab === 'offline')
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" wire:model="offlineMethod">
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile Money</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" wire:model="amount" placeholder="0.00" />
                    </div>

                    <div class="d-grid">
                        <button wire:click="processOfflinePayment" class="btn btn-primary">
                            Confirm Payment
                        </button>
                    </div>
                @endif

                <!-- Online Payment -->
                @if($tab === 'online')
                    <div class="text-center">
                        <p class="text-muted mb-3">Click below to pay securely with Paystack</p>
                        <button wire:click="initiatePaystack" wire:loading.attr="disabled" class="btn btn-primary w-100">
                            <span class="d-flex gap-2" wire:loading.remove wire:target="initiatePaystack">
                                <img src="{{ asset('assets/images/third-icons/paystack.png') }}" style="height: 20px;" alt="">
                                <span>Pay with Paystack</span>
                            </span>
                            <span wire:loading wire:target="initiatePaystack">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" wire:click="$dispatch('closeModal')">{{ __('Discard') }}</button>
        </div>
    </div>

    @script
    <script>
        $wire.on('openPaystackPopup', url => {
            let width = 600, height = 700;
            let left = (screen.width - width) / 2;
            let top = (screen.height - height) / 2;

            let paystackWindow = window.open(url, 'Paystack Payment', `width=${width},height=${height},top=${top},left=${left}`);

            // let interval = setInterval(() => {
            //     if (paystackWindow && paystackWindow.closed) {
            //         clearInterval(interval);
            //         $wire.dispatch('paymentCompleted', {reference: localStorage.getItem('paystack_payment_reference')});
            //     }
            // }, 1000);
        });
    </script>
    @endscript

</div>
