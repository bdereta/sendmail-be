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
        $mail = realpath($raw = __DIR__ . '/../config/mail.php') ?: $raw;
        $this->mergeConfigFrom($mail, 'mail');

        // add services config
        $services = realpath($raw = __DIR__ . '/../config/services.php') ?: $raw;
        $this->mergeConfigFrom($services, 'mail');

    }

    /**
     * Register Illuminate mailer instance.
     *
     * @return void
     */
    protected function registerIlluminateMailer()
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

