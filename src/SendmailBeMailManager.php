<?php

namespace BDereta\SendmailBe;

use Illuminate\Mail\MailManager;


class SendmailBeMailManager extends MailManager
{
    protected function createSendmailBeTransport()
    {
        // grab config keys
        $config = $this->app['config']->get('services.sendmail_be_mail', []);
        // return new SendmailBeTransport instance with config
        return new SendmailBeTransport($config['url'], $config['key']);
    }
}
