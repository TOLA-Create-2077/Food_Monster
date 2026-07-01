<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $pageTitle;
    public $currentPage;

    public function __construct($pageTitle = '', $currentPage = '')
    {
        $this->pageTitle = $pageTitle;
        $this->currentPage = $currentPage;
    }

    public function render()
    {
        return view('components.sidebar');
    }
}