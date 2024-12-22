<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class InputBox extends Component
{
    /**
     * Create a new component instance.
     */
    public $type;
    public $name;
    public $required;
    public $pattern;
    public $value;
    public $label;
    public $checked;
    public function __construct($type = 'text', $name = '', $required = null, $pattern = null, $value = null, $label = null, $checked = null)
    {
        $this->type = $type;
        $this->required = $required;
        $this->pattern = $pattern;
        $this->value = $value;
        $this->name = $name;

        if ($label == null) {
            $this->label = ucwords(str_replace('_', ' ', $name));
        } else {
            $this->label = $label;
        }
        if ($checked == true) {
            $this->checked = 'checked';
        } else {
            $this->checked = null;
        }
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-box');
    }
}
