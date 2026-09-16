<?php

namespace App\Providers;

use App\Models\CurrentWeek;
use App\Services\Sportsdata;
use Carbon\CarbonImmutable;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
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

        // Guzzle Client
        $this->app->bind(
            Client::class, function () {
                $config = [];
                $config['base_uri'] = config('services.sportsdata.base_url');
                return new Client($config);
            }
        );
        //EGov AD Auth
        $this->app->bind(
            Sportsdata::class, function () {
                return new Sportsdata(
                    $this->app->make(Client::class),
                    config('services.sportsdata.key')
                );
            }
        );
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
