<?php

use Illuminate\Support\Facades\Route;
use Pochocho\OktaSamlSso\Http\Controllers\OktaWebhookController;

Route::get(
    config('okta-saml-sso.webhooks.route_path'),
    [
        OktaWebhookController::class,
        'oktaVerification',
    ]
);

Route::post(
    config('okta-saml-sso.webhooks.route_path'),
    [
        OktaWebhookController::class,
        'processWebhook',
    ]
);
