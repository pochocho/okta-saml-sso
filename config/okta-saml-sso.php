<?php

return [

    'attribute_statements' => explode(
        ',',
        env(
            'OKTA_ATTRIBUTE_STATEMENTS',
            'first_name,last_name,email,groups'
        )
    ),

    'credential_paths' => [
        'certificate' => env('OKTA_CERTIFICATE_PATH', base_path('oktasso.crt')),
        'key' => env('OKTA_KEY_PATH', base_path('oktasso.key')),
        'idp_certificate' => env('OKTA_IDP_KEY_PATH', base_path('idp.cert')),
    ],

    'single_signon_url' => env('OKTA_SIGNON_URL'),

    'authenticatable_model' => env('OKTA_AUTHENTICATABLE_MODEL', "App\Models\User"),

    'authenticate_action' => Pochocho\OktaSamlSso\Actions\SamlUserAuthenticated::class,

    'login_redirect_route' => env('LOGIN_REDIRECT_ROUTE'),

    'webhooks' => [
        'enabled' => env('OKTA_WEBHOOK_ENABLED', false),
        'route_path' => env('OKTA_WEBHOOK_PATH', '/okta-webhook'),
        'authorization' => [
            'header' => env('OKTA_WEBHOOK_AUTH_HEADER', 'x-auth-key'),
            'secret' => env('OKTA_WEBHOOK_AUTH_SECRET'),
        ],
    ],
];
