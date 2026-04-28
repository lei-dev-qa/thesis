<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\View\Composers\UnviewedApplicationsComposer;
use App\View\Composers\UnreadMessagesComposer;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        View::composer('admin.components.sidebar', UnviewedApplicationsComposer::class);
        View::composer('admin.components.sidebar', UnreadMessagesComposer::class);

    }
}
