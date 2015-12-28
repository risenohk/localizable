<?php
/**
 * Created by PhpStorm.
 * User: ryanchan
 * Date: 28/12/2015
 * Time: 4:20 PM
 */

namespace Riseno\Localizable;


use Illuminate\Support\ServiceProvider;
use Riseno\Localizable\Console\GeneratorCommand;

/**
 * Class LocalizableServiceProvider
 * @package Riseno\Localizable
 */
class LocalizableServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['command.riseno.localizable.generate'] = $this->app->share(function ($app) {
            return new GeneratorCommand($app['files']);
        });

        $this->commands(['command.riseno.localizable.generate']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.riseno.localizable.generate'];
    }
}