<?php

namespace Modules\Pos\Livewire\Navbar\ControlPanel;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Modules\App\Livewire\Components\Navbar\Button\ActionButton;
use Modules\App\Livewire\Components\Navbar\Button\ActionDropdown;
use Modules\App\Livewire\Components\Navbar\ControlPanel;
use Modules\App\Livewire\Components\Navbar\SwitchButton;
use Modules\App\Services\ReportExportService;
use Modules\Pos\Models\Floor\FloorPlan;
use Modules\Pos\Models\Pos\Pos;

class FloorPanel extends ControlPanel
{
    public $floor;

    public function mount($floor = null, $isForm = false)
    {
        $this->showBreadcrumbs = true;
        $this->generateBreadcrumbs();
        $this->new = route('pos-floors.create');

        $restaurants = Pos::isCompany(current_company()->id)
        ->pluck('name', 'id') // now it's an array like [1 => 'House', 2 => 'Apartment']
        ->toArray();

        $this->filterTypes = [
            'pos_id' => $restaurants,
            'status' => [
                'active' => 'active',
                'inactive' => 'inactive',
            ],
        ];
        if($isForm){
            $this->showIndicators = true;
        }

        if($floor){
            $this->showIndicators = true;
            $this->floor = $floor;
            $this->isForm = true;
            $this->currentPage = $floor->reference;
        }else{
            $this->currentPage = 'Floor Plans';
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
            ActionDropdown::make('unarchive', 'Unarchive', 'unarchive', false, "fas fa-inbox"),
            ActionDropdown::make('delete', 'Delete', 'deleteSelectedItems', false, "fas fa-trash", true, "Do you really want to delete the selected items?"),
        ];
    }

    public function exportAll(){
        $exportService = new ReportExportService();

        $floors = FloorPlan::isCompany(current_company()->id)->get()
        ->map(function ($floor) {

            return [
                'restaurant' => $floor->pos->name,
                'name' => $floor->name,
            ];
        });

        $detailedSections = [
            'floors' => $floors,
        ];

        return $exportService->export("Floor Plans_export", [], $detailedSections);
    }

    public function exportSelected(){
        $exportService = new ReportExportService();

        $floors = FloorPlan::isCompany(current_company()->id)
        ->whereIn('id', $this->selected)->get()
        ->map(function ($floor) {

            return [
                'restaurant' => $floor->pos->name,
                'name' => $floor->name,
            ];
        });

        $detailedSections = [
            'floors' => $floors,
        ];

        return $exportService->export("Floor Plans_export", [], $detailedSections);
    }

    public function deleteSelectedItems(){

        FloorPlan::isCompany(current_company()->id)
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
