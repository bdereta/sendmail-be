<?php

namespace Bdereta\SendmailBe;

use Exception;
use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Swift_Mime_SimpleMessage;

class SendmailBeTransport extends Transport
{
    /**
     * API key.
     *
     */
    protected string $key;

    /**
     * The API URL to which to POST emails.
     *
     */
    protected string $url;

    /**
     * Create a new Custom transport instance.
     *
     * @param  string|null  $url
     * @param  string  $key
     * @return void
     */
    public function __construct(string $url, string $key)
    {
        $this->key = $key;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null): int
    {
        $this->beforeSendPerformed($message);

        $payload = $this->getPayload($message);

        try {
            // ignore ssl (esp when working in DEV/QA)
            $response = Http::withoutVerifying()->withHeaders([
                // Api Key - must be sent as X-Authorization header
                // Please generate a fresh API key for each app you build.
                // See Azure API-mail repo README on how to generate new keys.
                'X-Authorization' => $this->key // not to be confused with SendGrid API
                // post request body to Mail API
            ])->post($this->url, $payload);
            // API response. You should find 'success' key in every response. Values are boolean (true/false).
            // If success == false, 'errors' should provide enough information on why it failed.
            $response_body = json_decode($response->body());

            // if it fails,
            if (empty($response_body->success)) {
                // throw an exception and let controller deal with it
                throw new Exception($response->body() ?? 'Email sending failed.');
            }
            // if it does go through, store response into logs, just for reference
            Log::info($response->body());

            // this is part of Laravel standard process: it loops through arbitrary plugins
            $this->sendPerformed($message);

            // this is part of Laravel standard process: it counts and returns the number of recipients.
            return $this->numberOfRecipients($message);

        } catch (Exception $e) {
            // capture any HTTP (curl) errors
            Log::error($e->getMessage());
            // let's alert controller and let them decide how to handle it.
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get the HTTP payload for sending the message.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function getPayload(Swift_Mime_SimpleMessage $message): array
    {
        // from (email)
        if (!empty($message->getFrom())) {
            $payload['payload']['from']['email'] = key($message->getFrom());
        }

        // from (name)
        if (!empty(value($message->getFrom()))) {
            $payload['payload']['from']['name'] = value($message->getFrom());
        }

        // to
        if (!empty($message->getTo())) {
            $payload['payload']['to']['email'] = key($message->getTo());
        }
        // cc
        if (!empty($message->getCc())) {
            $payload['payload']['cc']['email'] = key($message->getCc());
        }
        // bcc
        if (!empty($message->getBcc())) {
            $payload['payload']['bcc']['email'] = key($message->getBcc());
        }
        // subject
        $payload['payload']['subject'] = $message->getSubject();

        // html
        $payload['payload']['message']['html'] = $message->getBody();

        // message children contains plain text, attachments, etc
        $children = $message->getChildren();

        if (!empty($children)) {

            foreach($children as $child) {

                // attachments
                if (get_class($child) === 'Swift_Attachment') {

                    $payload['payload']['attachments'][] = [
                        'content' => base64_encode($child->getBody()),
                        'filename' => $child->getFilename(),
                    ];
                }
                // plain text
                if (get_class($child) === 'Swift_MimePart') {
                    $payload['payload']['message']['text'] = $child->getBody();
                }
            }
        }

        return $payload;
    }

}
