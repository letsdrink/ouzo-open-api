<?php

namespace Ouzo\OpenApi\Util;

use ReflectionClass;
use stdClass;

class HashCodeBuilder
{
    private int $total;

    public function __construct(
        int $initialOddNumber = 17,
        private int $multiplierOddNumber = 37
    )
    {
        $this->total = $initialOddNumber;
    }

    public function append(mixed $value): HashCodeBuilder
    {
        if (is_null($value)) {
            $this->total = $this->total * $this->multiplierOddNumber;
        } else if (is_bool($value)) {
            $this->total = $this->total * $this->multiplierOddNumber + ($value ? 0 : 1);
        } else if (is_int($value)) {
            $this->total = $this->total * $this->multiplierOddNumber + $value;
        } else if (is_float($value)) {
            $value = is_nan($value) ? 0x7fc00000 : unpack('s', $value)[1];
            $this->total = $this->total * $this->multiplierOddNumber + $value;
        } else if (is_string($value)) {
            $h = 0;
            foreach (str_split($value) as $char) {
                $h = $h + ord($char);
            }
            $this->total = $this->total * $this->multiplierOddNumber + $h;
        } else if (is_array($value)) {
            foreach ($value as $element) {
                $this->append($element);
            }
        } else if (is_object($value)) {
            $reflectionClass = new ReflectionClass($value);
            if ($reflectionClass->hasMethod('hashCode')) {
                $this->total = $this->total * $this->multiplierOddNumber + $value->hashCode();
            } else {
                if ($value instanceof stdClass) {
                    $properties = get_object_vars($value);
                    foreach ($properties as $property) {
                        $this->append($property);
                    }
                } else {
                    $reflectionProperties = $reflectionClass->getProperties();
                    foreach ($reflectionProperties as $reflectionProperty) {
                        $reflectionProperty->setAccessible(true);
                        $tmp = $reflectionProperty->getValue($value);
                        $this->append($tmp);
                    }
                }
            }
        }

        return $this;
    }

    public function toHashCode(): int
    {
        return $this->total;
    }
}
