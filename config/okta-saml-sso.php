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
    ],

    'single_signon_url' => env('OKTA_SIGNON_URL'),

    'user_model' => env('OKTA_AUTHENTICATABLE_MODEL', "App\Models\User"),

    'authenticate_action' => Pochocho\OktaSamlSso\Events\SamlUserAuthenticated\SamlUserAuthenticated::class,

    'login_redirect_route' => env("LOGIN_REDIRECT_ROUTE"),
];
