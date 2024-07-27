<?php

namespace Pochocho\OktaSamlSso\Http\Controllers;

use Pochocho\OktaSamlSso\OktaEntity;

class OktaSamlController
{
    public function SamlUserAuthenticated(OktaEntity $oktaEntity): void
    {
        $action = config('okta-saml-sso.authenticate_action');

        (new $action)->handle($oktaEntity);
    }
}
