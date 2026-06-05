<?php

namespace App\Providers;

use App\Events\UserNotificationCreated;
use App\Mail\UserNotificationEmail;
use App\Support\Brand;
use App\Support\TransactionalMail;
use App\Models\Patient;
use App\Support\PatientApplicationNotifications;
use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use App\Observers\ProgramRegistrationObserver;
use App\Observers\RegistrationInvoiceObserver;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once base_path('bootstrap/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ProgramRegistration::observe(ProgramRegistrationObserver::class);
        RegistrationInvoice::observe(RegistrationInvoiceObserver::class);
        $this->registerSlowQueryLogging();

        // Force correct base URL when app is in subdirectory (e.g. /pinkme)
        $appUrl = config('app.url');
        if ($appUrl && parse_url($appUrl, PHP_URL_PATH) && parse_url($appUrl, PHP_URL_PATH) !== '/') {
            URL::forceRootUrl($appUrl);
        }
        if (is_string($appUrl) && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        } elseif (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        View::share('brandName', Brand::name());
        View::share('staffAccessNotice', Brand::staffAccessNotice());

        View::composer('patient.partials.sidebar', function ($view): void {
            $user = auth()->user();
            $view->with(
                'patientCanUseChat',
                $user ? Patient::userHasAssignedCaseManager($user) : false
            );
        });

        // Send email when a user receives a notification
        Event::listen(UserNotificationCreated::class, function (UserNotificationCreated $event): void {
            $notification = $event->notification;
            if (PatientApplicationNotifications::shouldSkipGenericNotificationEmail($notification)) {
                return;
            }
            $user = $notification->user ?? $notification->user()->first();
            if ($user && filled($user->email)) {
                TransactionalMail::send($user->email, new UserNotificationEmail($notification));
            }
        });
    }

    private function registerSlowQueryLogging(): void
    {
        if (! config('performance.log_enabled')) {
            return;
        }

        $thresholdMs = (int) config('performance.slow_query_ms', 120);

        DB::listen(function (QueryExecuted $query) use ($thresholdMs): void {
            if ($query->time < $thresholdMs) {
                return;
            }

            Log::warning('slow_query', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => (float) $query->time,
                'connection' => $query->connectionName,
            ]);
        });
    }
}
