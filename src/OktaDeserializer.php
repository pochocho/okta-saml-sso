<?php

namespace Pochocho\OktaSamlSso;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\X509Credential;
use LightSaml\Error\LightSamlSecurityException;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\XmlDSig\SignatureXmlReader;

class OktaDeserializer
{
    private ?X509Credential $credentials;

    public function __construct(
        private DeserializationContext $deserializationContext,
        private Response $response,
        private array $config
    ) {}

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

        $this->validateSignature($this->response->getSignature());

        if (! empty($this->credentials)) {
            return $this->decryptAssertions();
        }

        $assertion = $this->response->getFirstAssertion();

        $this->validateConditions($assertion->getConditions());

        return $assertion;
    }

    private function getPublicKey()
    {
        return KeyHelper::createPublicKey(
            X509Certificate::fromFile($this->config['idp_certificate'])
        );
    }

    private function validateSignature(SignatureXmlReader $signature)
    {
        try {
            $validSignature = $signature->validate($this->getPublicKey());
        } catch (LightSamlSecurityException $e) {
            abort(400, 'Invalid Request');
        }

        if (! $validSignature) {
            abort(400, 'Invalid SAML Signature');
        }
    }

    private function validateConditions(Conditions $conditions)
    {
        if ($conditions->getNotOnOrAfterTimestamp() <= time()) {
            abort(400, 'Expipred Request');
        }
    }

    private function decryptAssertions()
    {
        $decryptDeserializeContext = new DeserializationContext;
        /** @var \LightSaml\Model\Assertion\EncryptedAssertionReader $reader */
        $reader = $this->response->getFirstEncryptedAssertion();

        return $reader->decryptMultiAssertion([$this->credentials], $decryptDeserializeContext);
    }
}
