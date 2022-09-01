<?php

namespace App\Http\Livewire\Datatables;

use Livewire\Component;
use Livewire\WithPagination;

class BaseDatatable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPageOptions = ['25', '50', '100'];
    public $perPage = 25;
    public $search = '';
    public $sortBy = 'id';
    public $sortOrder = 'desc';
    public $bulkDeleteEnabled = false;
    public $columnsToggleable = false;
    public $bulkDeleteMessage = "Selected rows deleted";
    public $selectedRows = [];
    public $columns = [];
    public $routes = [];

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.datatables.base');
    }

    public function sort($column)
    {
        $sortable = $this->columns[$column]['sortable'] ?? false;
        if ($sortable){
            $this->sortBy = $this->columns[$column]['sort_by'] ?? $column;
            $this->sortOrder = $this->sortOrder == 'asc' ? 'desc' : 'asc';
        }
    }

    public function changeVisibility($column)
    {
        $this->columns[$column]['visible'] = !$this->columns[$column]['visible'];
    }

    public function deleteSelectedRows()
    {
        $this->model::destroy($this->selectedRows);
        $this->reset('selectedRows');
        $this->emit('flashNotification', $this->bulkDeleteMessage);
        $this->emit('rowsDeleted');
    }
}
