<?php

namespace BDereta\SendmailBe;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Mail\MailServiceProvider;

class SendmailBeServiceProvider extends MailServiceProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function register()
    {

        // add mail config
        $this->mergeConfigFrom(__DIR__ . '/../config/mail.php', 'mail');

        // add services config
        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');

        // initiate MailManager
        $this->app->singleton('mail.manager', function($app) {
            return new SendmailBeMailManager($app);
        });

        // create mailer
        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });

    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfig(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! \Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

}

