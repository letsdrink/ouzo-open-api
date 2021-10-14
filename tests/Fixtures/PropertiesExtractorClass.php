<?php

namespace Ouzo\Fixtures;

class PropertiesExtractorClass extends ParentPropertiesExtractorClass
{
    private $property1;

    private string|int $property2;

    private SubPropertiesExtractorClass $property3;

    /** @var SubPropertiesExtractorClass[] */
    private array $property4;
}
