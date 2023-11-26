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

    public array $tourImages;

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
        $this->tourImages = $this->project->tours
            ->mapWithKeys(fn ($tour) => [$tour->id => $tour->getFirstMediaUrl('thumbnail')])
            ->all();

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
        $data['toursArray'] = $this->project->tours->toKeyValuePair();
        $data['tourImages'] = $this->project->tours
            ->mapWithKeys(fn ($tour) => [$tour->id => $tour->getFirstMediaUrl('thumbnail')])
            ->all();

        return view('livewire.forms.layout-form', $data);
    }

    public function submit()
    {
        $this->validate();

        $this->layout->save();

        $this->close(andDispatch: [
            'refresh',
            'flashNotification' => ['message' => 'Layout created']
        ]);
    }

    public static function attributes(): array
    {
        return [
            'size' => '3xl'
        ];
    }
}
