<?php

namespace Pochocho\OktaSamlSso;

use Illuminate\Support\ServiceProvider;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\Response;

class OktaSamlSsoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $configPath = __DIR__.'/../config/okta-saml-sso.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'okta-saml-sso');

        if ($this->app->config['okta-saml-sso']['webhooks']['enabled']) {
            $this->registerWebhookRoutes();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/okta-saml-sso.php',
            'okta-saml-sso'
        );

        if (! $this->app->runningInConsole() || $this->app->runningUnitTests()) {
            $this->app->bind(OktaEntity::class, function ($app) {
                return new OktaEntity($app->config['okta-saml-sso']['attribute_statements']);
            });

            $this->app->bind(OktaDeserializer::class, function ($app) {
                return new OktaDeserializer(
                    new DeserializationContext,
                    new Response,
                    $app->config['okta-saml-sso']['credential_paths']
                );
            });

            $this->app->bind(OktaSaml::class, function ($app) {
                $request = $this->app->request;

                return new OktaSaml(
                    $request->input('SAMLResponse'),
                    $app->make(OktaDeserializer::class),
                    $app->make(OktaEntity::class),
                    null,
                );
            });
        }
    }

    protected function getConfigPath(): string
    {
        return config_path('okta-saml-sso.php');
    }

    private function registerWebhookRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/okta-webhooks.php');
    }
}
