<?php

namespace Pochocho\OktaSamlSso\Tests;

use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Pochocho\OktaSamlSso\Events\OktaWebhookReceived;
use Pochocho\OktaSamlSso\OktaSamlSsoServiceProvider;

class OktaWebhooksTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OktaSamlSsoServiceProvider::class,
        ];
    }

    protected function webhookEnabledSettings($app)
    {
        $app->config->set('okta-saml-sso.webhooks.enabled', true);
        $app->config->set('okta-saml-sso.webhooks.authorization.secret', '12345678');
    }

    #[Test]
    #[DefineEnvironment('webhookEnabledSettings')]
    public function it_can_verify_ownership_of_server()
    {
        $verifierCode = '🌯';

        $response = $this->get(
            '/okta-webhook',
            [
                'x-auth-key' => '12345678',
                'x-okta-verification-challenge' => $verifierCode,
            ]
        );

        $response->assertOk();

        $this->assertEquals($verifierCode, $response->json('verification'));
    }

    #[Test]
    #[DefineEnvironment('webhookEnabledSettings')]
    public function it_can_protects_verification_and_processing_endpoints_with_secret()
    {
        $validSecretCode = '12345678';
        $invalidSecret = 'naughty';

        $response = $this->get(
            '/okta-webhook',
            [
                'x-auth-key' => $invalidSecret,
            ]
        );

        $response->assertForbidden();

        $response = $this->get(
            '/okta-webhook',
            [
                'x-auth-key' => $validSecretCode,
            ]
        );

        $response->assertOk();

        $response = $this->post(
            '/okta-webhook',
            ['data' => []],
            [
                'x-auth-key' => $invalidSecret,
            ]
        );

        $response->assertForbidden();

        $response = $this->post(
            '/okta-webhook',
            ['data' => []],
            [
                'x-auth-key' => $validSecretCode,
            ]
        );

        $response->assertOk();
    }

    #[Test]
    #[DefineEnvironment('webhookEnabledSettings')]
    public function it_fires_event_to_process_webhooks()
    {
        Event::fake();

        $response = $this->post(
            '/okta-webhook',
            ['data' => []],
            [
                'x-auth-key' => '12345678',
            ]
        );

        $response->assertOk();
        Event::assertDispatched(OktaWebhookReceived::class);
    }
}
