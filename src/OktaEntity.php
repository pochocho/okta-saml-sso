<?php

namespace Pochocho\OktaSamlSso;

use Exception;

class OktaEntity
{
    private $attributes;

    public function __construct(private array $acceptedAttributes)
    {
    }

    public function fill(array $attributes)
    {
	    foreach ($attributes as $attribute) {
		    $this->{strtolower($attribute->getName())} = $attribute->getFirstAttributeValue();

		    if (str_contains($attribute->getNameFormat(), 'format:unspecified')) {
			    $this->{strtolower($attribute->getName())} = $attribute->getAllAttributeValues();
		    }
	    }

        return $this;
    }

    public function __get(string $attribute)
    {
        if (!in_array($attribute, $this->acceptedAttributes)) {
            throw new Exception("The {$attribute} Attribute is not configured");
        }

        return $this->attributes[$attribute];
    }

    public function __set(string $attribute, $attributeValue)
    {
        if (!in_array($attribute, $this->acceptedAttributes)) {
            throw new Exception("The {$attribute} Attribute is not configured");
        }

        $this->attributes[$attribute] = $attributeValue;
    }
}
