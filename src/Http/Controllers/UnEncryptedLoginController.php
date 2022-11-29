<?php

namespace Pochocho\OktaSamlSso\Http\Controllers;

use Pochocho\OktaSamlSso\OktaSaml;

class UnEncryptedLoginController extends OktaSamlController
{
    public function __invoke(OktaSaml $oktaSaml)
    {
        $this->SamlUserAuthenticated($oktaSaml->getEncryptedEntity());

        return redirect()->intended(
            route(config('okta-saml-sso.login_redirect_route'))
        );
    }
}
