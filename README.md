### GCE Sendmail API integration package for Laravel

[Laravel Mailable](https://laravel.com/docs/8.x/mail) is the preferred method for sending emails. This 
package enables your Laravel instance to utilize [API-sendmail-be](https://dev.azure.com/gcedigitalmarketing/Web%20Marketing/_git/API-sendmail-be) with Laravel Mailable. 

#### Requirements

* Laravel >=8

#### Installation

1. `composer require bdereta/sendmail-be`
2. Add the following variables to local .env: 
   * MAIL_URL=https://mkt-api.gcu.edu/sendmail-be/v1/send
   * MAIL_API_KEY= (see [Sendmail-BE repo for instructions on how to obtain an API key](https://dev.azure.com/gcedigitalmarketing/Web%20Marketing/_git/API-sendmail-be?anchor=managing-sendmail-api-keys) )

Note: MAIL_URL can be optional because the production url is defaulted in the config. However, you can define the QA/DEV url if you need to test your application in different environments.

#### Usage

1. Create new Mailable within your Laravel project: `php artisan make:mail ContactMail`
2. A boilerplate App\Mail\ContactMail class will be generated. For more information on how to write Mailable, visit [Laravel official documentation](https://laravel.com/docs/8.x/mail#writing-mailables)

