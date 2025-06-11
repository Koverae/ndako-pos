<?php

namespace Modules\Pos\Livewire\Modal;

use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;
use Modules\App\Services\PaymentGateway\PaystackService;
use Modules\Pos\Models\Order\PosOrder;

class PaymentModal extends ModalComponent
{
    public PosOrder $order;
    private PaystackService $paystackService;
    public string $tab = 'offline';
    public ?string $offlineMethod = null;
    public ?string $paymentMethod = null;
    public float $amount = 0;

    public function mount(PosOrder $order){
        $this->order = $order;
    }

    public function boot(PaystackService $paystackService){
        $this->paystackService = $paystackService;
    }

    public function initiatePaystack(){

        $extraData = [
            'method' => 'paystack',
            'orderId' => $this->order->id
        ];

        $responseData = $this->paystackService->initializePayment($this->order->guest->name ?? 'Brian Mwangi', $this->order->guest->email ?? 'brianmwangi@gmail.com', $this->order->total_amount, $extraData);
        return $this->dispatch('openPaystackPopup', $responseData->data->authorization_url);
        // return $this->dispatch('openPaystackTab', $responseData->data->authorization_url);
    }

    #[On('paymentCompleted')]
    public function paymentCompleted()
    {
        $reference = session('paystack_payment_reference');
        session()->forget('paystack_payment_reference'); // Destroy session after retrieving
        // Verify payment from Paystack
        $paystackKey = settings()->paystack_secret_key;

        $response = Http::withToken($paystackKey)->get("https://api.paystack.co/transaction/verify/{$reference}");

        $responseData = $response->json();

        if (isset($responseData['data']) && $responseData['data']['status'] === 'success') {
            session()->flash('success', 'Payment successful!');
        } else {
            session()->flash('error', 'Payment failed!');
        }
    }

    #[On('checkPaymentStatus')]
    public function checkPaymentStatus()
    {
        $reference = session('paystack_reference') ?? request()->query('reference');

        if (!$reference) {
            session()->flash('error', 'Payment was not completed.');
            return;
        }

        $paystackKey = config('services.paystack.secret');

        $response = Http::withToken($paystackKey)->get("https://api.paystack.co/transaction/verify/{$reference}");

        $responseData = $response->json();

        if (isset($responseData['data']) && $responseData['data']['status'] === 'success') {
            session()->flash('success', 'Payment successful!');
            $this->order->payment_status = true;
        } else {
            session()->flash('error', 'Payment failed!');
        }
    }

    public function render()
    {
        return view('pos::livewire.modal.payment-modal');
    }
}
