<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public $label;
    public $name;
    public $value;
    public $id;
    public $checked;

    public function __construct($label, $name, $value, $id, $checked = false)
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;
        $this->checked = $checked;
    }

    public function render()
    {
        return view('components.checkbox');
    }
}