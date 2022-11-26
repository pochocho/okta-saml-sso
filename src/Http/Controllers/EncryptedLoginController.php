<?php

namespace Pochocho\OktaSamlSso\Http\Controllers;

use Pochocho\OktaSamlSso\Actions\SamlUserAuthenticated;
use Pochocho\OktaSamlSso\OktaSaml;

class EncryptedLoginController
{
    public function __invoke(OktaSaml $oktaSaml)
    {

        (new SamlUserAuthenticated())->handle($oktaSaml->getEncryptedEntity());

        return redirect()->intended(
            route(config('okta-saml-sso.login_redirect_route'))
        );
    }
}
