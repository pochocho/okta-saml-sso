<?php

namespace Pochocho\OktaSamlSso\Tests;

use Exception;
use LightSaml\Model\Assertion\Attribute;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    #[DefineEnvironment('limitedAttributeStatements')]
    public function it_fails_to_fill_if_unexpected_attributes()
    {
        $oktaEntity = app()->make(OktaEntity::class);

        $samlAttributes = $this->generateAttributes();

        $this->expectException(Exception::class);

        $oktaEntity->fill(
            $samlAttributes
        );
    }

    #[Test]
    public function it_fills_if_all_attributes_confirgured()
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

    #[Test]
    public function it_treats_unspecified_formats_to_arrays()
    {
        $oktaEntity = app()->make(OktaEntity::class);

        $samlAttributes = $this->generateAttributes();

        $oktaEntity->fill(
            $samlAttributes
        );

        $this->assertIsArray($oktaEntity->groups);
    }

    #[Test]
    public function it_treats_basic_formats_as_strings()
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
