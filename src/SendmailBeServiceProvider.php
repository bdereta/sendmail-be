<?php

namespace BDereta\SendmailBe;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Mail\MailServiceProvider;

class SendmailBeServiceProvider extends MailServiceProvider
{

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // add mail config
        $this->mergeConfigFrom(__DIR__ . '/../config/mail.php', 'mail');

        // add services config
        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('mail.manager', function($app) {
            return new SendmailBeMailManager($app);
        });

        // Copied from Illuminate\Mail\MailServiceProvider
        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });

    }

}

