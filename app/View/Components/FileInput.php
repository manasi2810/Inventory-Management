<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FileInput extends Component
{
    public $label;
    public $name;
    public $multiple;

    public function __construct(
        $label,
        $name,
        $multiple = false
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->multiple = $multiple;
    }

    public function render()
    {
        return view('components.file-input');
    }
}