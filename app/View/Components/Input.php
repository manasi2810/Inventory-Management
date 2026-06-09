<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public string $label;
    public string $name;
    public string $type;
    public $value;  

    public function __construct(
        string $label,
        string $name,
        string $type = 'text',
        $value = null  
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->value = $value ?? '';
    }

    public function render()
    {
        return view('components.input');
    }
}