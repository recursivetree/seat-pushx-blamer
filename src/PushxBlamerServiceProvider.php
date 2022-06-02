<?php

namespace RecursiveTree\Seat\PushxBlamer;

use Seat\Services\AbstractSeatPlugin;

use Illuminate\Support\Facades\Artisan;
use RecursiveTree\Seat\PushxBlamer\Jobs\UpdatePushxQueue;


class PushxBlamerServiceProvider extends AbstractSeatPlugin
{
    public function boot(){
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }

        $this->loadViewsFrom(__DIR__ . '/resources/views/', 'pushxblamer');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        Artisan::command('pushxblamer:update {--sync}', function () {
            if ($this->option("sync")) {
                UpdatePushxQueue::dispatchNow();
            } else {
                UpdatePushxQueue::dispatch();
            }
        });

        //UpdatePushxQueue::dispatch();
    }

    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/Config/pushxblamer.sidebar.php','package.sidebar');
        $this->registerPermissions(__DIR__ . '/Config/pushxblamer.permissions.php', 'pushxblamer');
    }

    public function getName(): string
    {
        return 'SeAT Pushx Blamer';
    }

    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/recursivetree/seat-pushx-blamer';
    }

    public function getPackagistPackageName(): string
    {
        return 'seat-pushx-blamer';
    }

    public function getPackagistVendorName(): string
    {
        return 'recursivetree';
    }
}