<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use App\Console\Commands\SendMeetingReminder;

class ZoomServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerCommands();

        $this->app->booted(function () {
            $this->scheduleCommands();
        });

    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/zoom');

        $this->loadTranslationsFrom($langPath, 'zoom');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register artisan commands
     */
    private function registerCommands(): void
    {
        $this->commands([
            SendMeetingReminder::class,
        ]);
    }

    public function scheduleCommands()
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->command('send-zoom-meeting-reminder')->everyMinute();
    }
}
