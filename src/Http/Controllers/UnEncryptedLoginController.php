<?php

namespace Pochocho\OktaSamlSso\Http\Controllers;

use Pochocho\OktaSamlSso\Actions\SamlUserAuthenticated;
use Pochocho\OktaSamlSso\OktaSaml;

class UnEncryptedLoginController
{
    public function __invoke(OktaSaml $oktaSaml)
    {
        new SamlUserAuthenticated($oktaSaml->getEntity());
        
        return redirect()->intended(
            route(config('okta-saml-sso.login_redirect_route'))
        );
    }
}
