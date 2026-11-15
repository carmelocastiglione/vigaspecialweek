<?php

namespace App\Providers;

use App\Models\Activity;
use App\View\Composers\LayoutComposer;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerViewComposers();

        // Spatie può autorizzare prima di questo controllo se esiste un permesso con lo stesso nome dell'abilità.
        Gate::before(function ($user, $ability, array $arguments) {
            // Il primo argomento è il modello interessato o la sua classe.
            $subject = $arguments[0] ?? null;

            if (($subject === Activity::class || $subject instanceof Activity)
                && in_array($ability, ['browseStudentActivities', 'likeActivityAsStudent'], true)) {
                // Per queste azioni essere admin non basta: null lascia decidere ai controlli successivi e alla policy.
                return null;
            }

            // Per le altre azioni autorizza gli admin; null lascia decidere ai controlli successivi e alle policy.
            return $user->hasRole('admin') ? true : null;
        });
    }

    /**
     * Register view composers.
     */
    protected function registerViewComposers(): void
    {
        View::composer('home', LayoutComposer::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
