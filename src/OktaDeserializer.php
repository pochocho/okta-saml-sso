<?php

namespace Pochocho\OktaSamlSso;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\X509Credential;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\Response;

class OktaDeserializer
{
    private ?X509Credential $credentials;

    public function __construct(
        private DeserializationContext $deserializationContext,
        private Response $response,
        private array $config
    ) {
    }

    public function encrypted()
    {
        $this->credentials = new X509Credential(
            X509Certificate::fromFile($this->config['certificate']),
            KeyHelper::createPrivateKey($this->config['key'], '', true)
        );
    }

    public function deserialize($samlResponse)
    {
        $this->deserializationContext
            ->getDocument()
            ->loadXML(base64_decode($samlResponse));

        $this->response->deserialize(
            $this->deserializationContext->getDocument()->firstChild,
            $this->deserializationContext
        );

        if (!empty($this->credentials)) {
            return $this->decryptAssertions();
        }

        return $this->response->getFirstAssertion();
    }

    private function decryptAssertions()
    {
        $decryptDeserializeContext = new DeserializationContext();
        /** @var \LightSaml\Model\Assertion\EncryptedAssertionReader $reader */
        $reader = $this->response->getFirstEncryptedAssertion();
        return $reader->decryptMultiAssertion([$this->credentials], $decryptDeserializeContext);
    }
}
