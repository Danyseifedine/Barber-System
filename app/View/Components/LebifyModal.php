<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LebifyModal extends Component
{
    /**
     * Create a new component instance.
     */
    public $modalId;
    public $size;
    public $title;
    public $showCloseButton;
    public $showXButton;
    public $submitButtonId;
    public $submitFormId;
    public $submitButtonText;
    public $backdrop;
    public $showSubmitButton;

    public function __construct(
        $modalId = 'default-modal',
        $size = 'modal-lg',
        $title = 'Modal Title',
        $showCloseButton = true,
        $showXButton = true,
        $submitButtonId = 'modal-submit',
        $submitFormId = 'modal-form',
        $submitButtonText = 'Submit',
        $backdrop = 'static',
        $showSubmitButton = true
    ) {
        $this->modalId = $modalId;
        $this->size = $size;
        $this->title = $title;
        $this->showCloseButton = $showCloseButton;
        $this->showXButton = $showXButton;
        $this->submitButtonId = $submitButtonId;
        $this->submitFormId = $submitFormId;
        $this->submitButtonText = $submitButtonText;
        $this->backdrop = $backdrop;
        $this->showSubmitButton = $showSubmitButton;
    }

    public function render()
    {
        return view('components.lebify-modal');
    }
}
