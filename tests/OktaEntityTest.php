<?php

namespace Pochocho\OktaSamlSso\Tests;

use Exception;
use LightSaml\Model\Assertion\Attribute;
use Orchestra\Testbench\TestCase;
use Pochocho\OktaSamlSso\OktaEntity;
use Pochocho\OktaSamlSso\OktaSamlSsoServiceProvider;

class OktaEntityTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OktaSamlSsoServiceProvider::class,
        ];
    }

    protected function limitedAttributeStatements($app)
    {
        $app->config->set('okta-saml-sso.attribute_statements', ['first_name', 'last_name']);
    }

    private function generateAttributes()
    {
        $attributes = [
            'first_name' => [
                'value' => 'John',
                'format' => 'attrname-format:basic',
            ],
            'last_name' => [
                'value' => 'Doe',
                'format' => 'attrname-format:basic',
            ],
            'email' => [
                'value' => 'johndoe@email.com',
                'format' => 'attrname-format:basic',
            ],
            'groups' => [
                'value' => ['a', 'b', 'c'],
                'format' => 'attrname-format:unspecified',
            ],
        ];

        $samlAttributes = [];
        foreach ($attributes as $attributeKey => $attribute) {
            $samlAttribue = new Attribute($attributeKey, $attribute['value']);
            $samlAttribue->setNameFormat($attribute['format']);
            $samlAttributes[] = $samlAttribue;
        }

        return $samlAttributes;
    }

    /**
     * @test
     *
     * @define-env limitedAttributeStatements
     */
    public function itFailsToFillIfUnexpectedAttributes()
    {
        $oktaEntity = app()->make(OktaEntity::class);

        $samlAttributes = $this->generateAttributes();

        $this->expectException(Exception::class);

        $oktaEntity->fill(
            $samlAttributes
        );
    }

    /**
     * @test
     */
    public function itFillsIfAllAttributesConfirgured()
    {
        $oktaEntity = app()->make(OktaEntity::class);

        $samlAttributes = $this->generateAttributes();

        $oktaEntity->fill(
            $samlAttributes
        );

        $this->assertNotNull($oktaEntity->first_name);
        $this->assertNotNull($oktaEntity->last_name);
        $this->assertNotNull($oktaEntity->email);
        $this->assertNotNull($oktaEntity->groups);
    }

    /**
     * @test
     */
    public function itTreatsUnspecifiedFormatsToArrays()
    {
        $oktaEntity = app()->make(OktaEntity::class);

        $samlAttributes = $this->generateAttributes();

        $oktaEntity->fill(
            $samlAttributes
        );

        $this->assertIsArray($oktaEntity->groups);
    }

    /**
     * @test
     */
    public function itTreatsBasicFormatsAsStrings()
    {
        $oktaEntity = app()->make(OktaEntity::class);

        $samlAttributes = $this->generateAttributes();

        $oktaEntity->fill(
            $samlAttributes
        );

        $this->assertIsString($oktaEntity->first_name);
        $this->assertIsString($oktaEntity->last_name);
        $this->assertIsString($oktaEntity->email);
    }
}
