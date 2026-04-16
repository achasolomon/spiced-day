<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ChecklistItem extends Component
{
    public $name;
    public $label;
    public $description;
    public $critical;

    public function __construct($name, $label, $description, $critical = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->critical = $critical;
    }

    public function render()
    {
        return view('components.checklist-item');
    }
}