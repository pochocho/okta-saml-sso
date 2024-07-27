<?php

namespace Pochocho\OktaSamlSso;

use Exception;

class OktaEntity
{
    private $attributes;

    public function __construct(private array $acceptedAttributes) {}

    public function fill(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $name = strtolower($attribute->getName());
            $this->$name = $this->getAttributeValue($attribute);
        }

        return $this;
    }

    private function getAttributeValue($attribute)
    {
        if (str_contains($attribute->getNameFormat(), 'format:unspecified')) {
            return $attribute->getAllAttributeValues();
        }

        return $attribute->getFirstAttributeValue();
    }

    public function __get(string $attribute)
    {
        if (! in_array($attribute, $this->acceptedAttributes)) {
            throw new Exception("The {$attribute} Attribute is not configured");
        }

        return $this->attributes[$attribute];
    }

    public function __set(string $attribute, $attributeValue)
    {
        if (! in_array($attribute, $this->acceptedAttributes)) {
            throw new Exception("The {$attribute} Attribute is not configured");
        }

        $this->attributes[$attribute] = $attributeValue;
    }
}
