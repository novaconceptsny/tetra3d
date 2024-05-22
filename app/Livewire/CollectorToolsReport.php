<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CollectorToolsReport extends Component
{
    public $content = '';

    public function render(): View
    {
        $this->getCollectorLogs();
        return view('livewire.collector-tools-report');
    }

    public function getCollectorLogs(): void
    {
        try {
            $this->content = file_get_contents(storage_path('logs/collector.log'));
        } catch (\Exception){
            $this->content = 'No logs found';
        }
    }
}
