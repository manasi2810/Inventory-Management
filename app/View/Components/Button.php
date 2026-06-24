<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $type;
    public $color;
    public $icon;

    public function __construct(
        $type = 'button',
        $color = 'primary',
        $icon = ''
    ) {
        $this->type = $type;
        $this->color = $color;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.button');
    }
}