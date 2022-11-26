<?php

namespace Pochocho\OktaSamlSso\Actions;

use App\Models\User;
use Pochocho\OktaSamlSso\OktaEntity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SamlUserAuthenticated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function handle(OktaEntity $oktaEntity)
    {

        $model = $this->getUserModel();

        $user = $model::firstOrCreate(
            [
                'email' => $oktaEntity->email,
            ],
            [
                'name' => "{$oktaEntity->first_name} {$oktaEntity->last_name}",
                'password' => Str::random(20),
            ]
        );

        Auth::login($user);
    }

    private function getUserModel()
    {
        return config('okta-saml-sso.user_model');
    }
}
