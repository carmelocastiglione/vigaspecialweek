<?php

namespace App\View\Composers;

use App\Services\SettingsService;
use Illuminate\View\View;

class LayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'isMaintenanceMode' => class_exists(SettingsService::class)
                && SettingsService::isMaintenanceMode()
                && auth()->check()
                && auth()->user()?->isAdmin(),
            'isImpersonating' => auth()->check() && auth()->user()?->isImpersonating(),
        ]);
    }
}
