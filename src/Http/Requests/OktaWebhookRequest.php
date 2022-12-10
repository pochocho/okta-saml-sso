<?php

namespace Pochocho\OktaSamlSso\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OktaWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $authCode = $this->header(config('okta-saml-sso.webhooks.authorization.header'));

        return $authCode === config('okta-saml-sso.webhooks.authorization.secret');
    }

    public function rules(){
        return [];
    }
}
