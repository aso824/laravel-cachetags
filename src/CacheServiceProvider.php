<?php

namespace Aso824\CacheTags;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Enable cache tags on file driver by replacing it with custom cache store
        Cache::extend('file', function () {
            return Cache::repository(new FileStore($this->app['files'], config('cache.stores.file.path')));
        });
    }
}