<?php

namespace App\Livewire;

use App\Models\Surface;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class EditableField extends Component
{
    public $value;
    public $originalValue;
    public $editing = false;
    public $field;
    public Model $model;
    public $element = 'span';
    public $permission = 'access-backend';

    protected $rules = [
        'value' => 'required|min:3',
    ];

    public function mount(Model $model)
    {
        $this->value = $model->{$this->field};
        $this->originalValue = $this->value;
    }

    public function updating($name, $value)
    {
        if ($name === 'editing' && $value === true) {
            $this->originalValue = $this->value;
        }
    }

    public function render()
    {
        return view('livewire.editable-field');
    }

    public function updateValue()
    {
        $this->validateOnly('field');

        $this->model->update([
            $this->field => $this->value
        ]);

        $this->editing = false;
    }
}
