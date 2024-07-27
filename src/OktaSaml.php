<?php

namespace Pochocho\OktaSamlSso;

use Exception;

class OktaSaml
{
    public function __construct(
        private ?string $samlResponse,
        private OktaDeserializer $oktaDeserializer,
        private OktaEntity $oktaEntity,
        private ?array $config = null
    ) {}

    public function getEntity(): OktaEntity
    {
        if (empty($this->samlResponse)) {
            throw new Exception('Missing SAML Response');
        }

        $assertion = $this->oktaDeserializer->deserialize($this->samlResponse);

        return $this->oktaEntity
            ->fill(
                $assertion->getFirstAttributeStatement()
                    ->getAllAttributes()
            );
    }

    public function getEncryptedEntity(): OktaEntity
    {
        $this->oktaDeserializer->encrypted();

        return $this->getEntity();
    }
}
