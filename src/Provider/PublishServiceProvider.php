<?php

namespace Xbxf\Dingtalk\Provider;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class PublishServiceProvider extends IlluminateServiceProvider
{
    public function boot()
    {
        $this->publishes([realpath(__DIR__.'/../../config/dingtalk.php') => config_path('dingtalk.php')]);
    }
}
