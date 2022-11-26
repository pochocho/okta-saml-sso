<?php

namespace Pochocho\OktaSamlSso\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;

class SsoAuthenticate extends AuthenticateMiddleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return config('okta-saml-sso.single_signon_url');
        }
    }
}
