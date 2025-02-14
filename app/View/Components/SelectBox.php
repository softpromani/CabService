<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SelectBox extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $value;
    public $label;
    public $options;
    public $required;
    public $multiple;
    public function __construct($name,$options=[],$multiple=false,$value=null,$required=null,$label=null)
    {
        $this->required = $required;
        $this->value = $value;
        $this->name = $name;
        $this->options=$options;
        $this->multiple=$multiple;
        if ($label == null) {
            $this->label = ucwords(str_replace('_', ' ', $name));
        } else {
            $this->label = $label;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-box');
    }
}
