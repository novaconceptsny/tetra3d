<?php

namespace App\Livewire\Forms;

use App\Models\Layout;
use App\Models\Project;
use App\Models\Tour;
use WireElements\Pro\Components\Modal\Modal;

class LayoutForm extends Modal
{
    public Project|int $project;
    public Layout|int $layout;

    public string $heading;
    public $editing = false;

    public function rules(): array
    {
        return [
            'layout.name' => 'required',
            'layout.tour_id' => 'required',
            'layout.project_id' => 'required',
            'layout.user_id' => 'required',
        ];
    }

    public function mount(Project $project, Layout $layout)
    {
        $this->project = $project;
        $this->layout = $layout;

        $this->heading = 'Create Layout';

        if ($this->layout->id){
            $this->editing = true;
            $this->heading = 'Edit Layout';
        } else {
            $this->layout->user_id = auth()->id();
            $this->layout->project_id = $project->id;
            $this->layout->tour_id = '';
        }
    }

    public function render()
    {
        $data = array();
        $data['tours'] = $this->project->tours->toKeyValuePair();

        return view('livewire.forms.layout-form', $data);
    }

    public function submit()
    {
        $this->validate();

        $this->layout->save();

        $this->close();
        $this->dispatch('flashNotification', message: __('Layout created'));
    }
}
