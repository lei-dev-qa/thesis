<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Application\Application;

class UnviewedApplicationsComposer
{
    public function compose(View $view): void
    {
        $unviewedCount = Application::where('status', Application::STATUS_PENDING)
            ->whereDoesntHave('views')
            ->count();
        
        $view->with('unviewedApplicationsCount', $unviewedCount);
    }
}

