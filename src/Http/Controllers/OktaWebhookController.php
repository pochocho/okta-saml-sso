<?php

namespace Pochocho\OktaSamlSso\Http\Controllers;

use Pochocho\OktaSamlSso\Events\OktaWebhookReceived;
use Pochocho\OktaSamlSso\Http\Requests\OktaWebhookRequest;

class OktaWebhookController
{
    public function oktaVerification(OktaWebhookRequest $request)
    {
        if ($request->hasHeader('x-okta-verification-challenge')) {
            return response()->json(
                [
                    'verification' => $request->header('x-okta-verification-challenge'),
                ]
            );
        }
    }

    public function processWebhook(OktaWebhookRequest $request)
    {
        $eventMeta = $request->except('data');
        $eventData = $request->only('data');

        if (isset($eventData['data'])) {
            event(new OktaWebhookReceived($eventMeta, $eventData['data']));
        }
    }
}
