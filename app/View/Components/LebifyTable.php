<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LebifyTable extends Component
{
    public $id;
    public $create;
    public $selected;
    public $filter;
    public $columns;
    public $showCheckbox;
    public $showSearch;
    public $showColumnVisibility;
    public $columnVisibilityPlacement;
    public $columnSettingsTitle;
    public $columnToggles;
    public $tableClass;
    public $searchPlaceholder;
    public $selectedText;
    public $selectedActionButtonClass;
    public $selectedActionButtonText;
    public $selectedAction;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        bool $create = true,
        bool $selected = true,
        bool $filter = true,
        array $columns = [],
        bool $showCheckbox = true,
        bool $showSearch = true,
        bool $showColumnVisibility = true,
        string $columnVisibilityPlacement = 'bottom-end',
        string $columnSettingsTitle = 'Column Settings',
        string $columnToggles = '',
        string $tableClass = '',
        string $searchPlaceholder = 'Search...',
        string $selectedText = 'Selected',
        string $selectedActionButtonClass = 'btn-danger',
        string $selectedActionButtonText = 'Delete Selected',
        string $selectedAction = ''
    ) {
        $this->id = $id;
        $this->create = $create;
        $this->selected = $selected;
        $this->filter = $filter;
        $this->columns = $columns;
        $this->showCheckbox = $showCheckbox;
        $this->showSearch = $showSearch;
        $this->showColumnVisibility = $showColumnVisibility;
        $this->columnVisibilityPlacement = $columnVisibilityPlacement;
        $this->columnSettingsTitle = $columnSettingsTitle;
        $this->columnToggles = $columnToggles;
        $this->tableClass = $tableClass;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->selectedText = $selectedText;
        $this->selectedActionButtonClass = $selectedActionButtonClass;
        $this->selectedActionButtonText = $selectedActionButtonText;
        $this->selectedAction = $selectedAction;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lebify-table');
    }
}
