<?php

namespace Pochocho\OktaSamlSso\Actions;

use App\Models\User;
use Pochocho\OktaSamlSso\OktaEntity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $model = $this->getAuthenticatableModel();

        $user = $model::firstOrCreate(
            [
                'email' => $oktaEntity->email,
            ],
            [
                'name' => "{$oktaEntity->first_name} {$oktaEntity->last_name}",
                'password' => Hash::make(Str::random(20)),
            ]
        );

        Auth::login($user);
    }

    private function getAuthenticatableModel()
    {
        return config('okta-saml-sso.authenticatable_model');
    }
}
