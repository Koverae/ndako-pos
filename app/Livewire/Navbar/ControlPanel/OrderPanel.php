<?php

namespace Modules\Pos\Livewire\Navbar\ControlPanel;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Modules\App\Livewire\Components\Navbar\Button\ActionButton;
use Modules\App\Livewire\Components\Navbar\Button\ActionDropdown;
use Modules\App\Livewire\Components\Navbar\ControlPanel;
use Modules\App\Livewire\Components\Navbar\SwitchButton;
use Modules\App\Services\ReportExportService;
use Modules\Pos\Models\Order\PosOrder;
use Modules\Pos\Models\Order\PosOrderDetail;
use Modules\Pos\Models\Pos\Pos;

class OrderPanel extends ControlPanel
{
    public $order;

    public function mount($order = null, $isForm = false)
    {
        $this->showBreadcrumbs = true;
        $this->generateBreadcrumbs();
        // $this->new = route('products.create');

        $restaurants = Pos::isCompany(current_company()->id)
        ->pluck('name', 'id') // now it's an array like [1 => 'House', 2 => 'Apartment']
        ->toArray();

        $this->filterTypes = [
            'pos_id' => $restaurants,
            'status' => [
                'ongoing' => 'on going',
                'receipt' => 'receipt',
                'cancelled' => 'cancelled',
            ],
        ];
        if($isForm){
            $this->showIndicators = true;
        }

        if($order){
            $this->showIndicators = true;
            $this->order = $order;
            $this->isForm = true;
            $this->currentPage = $order->reference;
        }else{
            $this->currentPage = 'Orders';
        }

    }

    public function switchButtons() : array
    {
        return  [
            // make($key, $label)
            SwitchButton::make('lists',"switchView('lists')", "bi-list-task")
        ];
    }

    public function actionButtons(): array
    {
        return [
            ActionButton::make('export', 'Export All', 'exportAll', false, "fas fa-download"),
            ActionButton::make('import', 'Import Records', 'importRecords', false, "fas fa-upload"),
        ];
    }

    public function actionDropdowns(): array
    {
        return [
            ActionDropdown::make('export', 'Export', 'exportSelected', false, "fas fa-download"),
            ActionDropdown::make('archive', 'Archive', 'archive', false, "fas fa-archive"),
            ActionDropdown::make('unarchive', 'Unarchive', 'unarchive', false, "fas fa-inbox"),
            ActionDropdown::make('delete', 'Delete', 'deleteSelectedItems', false, "fas fa-trash", true, "Do you really want to delete the selected items?"),
        ];
    }

    public function exportAll(){
        $exportService = new ReportExportService();

        $orders = PosOrder::isCompany(current_company()->id)->get()
        ->map(function ($order) {

            return [
                'reference' => $order->reference,
                'order_id' => $order->receipt_number,
                'restaurant' => $order->pos->name ?? "N/A",
                'table' => $order->table->table_name ?? "N/A",
                'cashier' => $order->cashier->name ?? "N/A",
                'guest' => $order->guest->name ?? "N/A",
                'total_amount' => $order->total_amount,
            ];
        });

        $detailedSections = [
            'orders' => $orders,
        ];

        return $exportService->export("Orders_export", [], $detailedSections);
    }

    public function exportSelected(){
        $exportService = new ReportExportService();

        $orders = PosOrder::isCompany(current_company()->id)
        ->whereIn('id', $this->selected)->get()
        ->map(function ($order) {

            return [
                'reference' => $order->reference,
                'order_id' => $order->order_id,
                'restaurant' => $order->pos->name ?? "N/A",
                'table' => $order->table->table_name ?? "N/A",
                'cashier' => $order->cashier->name ?? "N/A",
                'guest' => $order->guest->name ?? "N/A",
                'total_amount' => $order->total_amount,
            ];
        });

        $detailedSections = [
            'orders' => $orders,
        ];

        return $exportService->export("Orders_export", [], $detailedSections);
    }

    public function deleteSelectedItems(){
        foreach($this->selected as $selected){
            PosOrderDetail::isCompany(current_company()->id)
                ->whereIn('pos_order_id', $this->selected)
                ->delete();
        }

        PosOrder::isCompany(current_company()->id)
            ->whereIn('id', $this->selected)
            ->delete();

        LivewireAlert::title('Items deleted!')
        ->text('Selected items were deleted successfully!')
        ->success()
        ->position('top-end')
        ->timer(4000)
        ->toast()
        ->show();

        return $this->redirect(route('orders.lists'), navigate:true);
    }

    public function importRecords(){
        return $this->redirect(route('import.records', 'mod_pos_orders'), true);
    }
}
