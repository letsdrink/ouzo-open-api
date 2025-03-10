<?php

namespace Ouzo\Fixtures;

use Ouzo\Fixtures\Polymorphism\Message;
use Ouzo\OpenApi\Attributes\Schema;

class SampleClass
{
    private string $scalar;
    private ?int $nullableScalar;
    #[Schema(nullable: false)]
    private ?int $nullableScalarWhenNullableIsFalse;
    /** @var bool */
    private $scalarWithTypeInPhpDoc;
    /** @var bool|null */
    private $nullableScalarWithTypeInPhpDoc;

    /** @var string[] */
    private array $arrayOfScalars;
    /** @var string[]|null */
    private ?array $nullableArrayOfScalars;
    /** @var string[]|null */
    #[Schema(nullable: false)]
    private ?array $nullableArrayOfScalarsWhenNullableIsFalse;
    /** @var string[] */
    private $arrayOfScalarsWithTypeInPhpDoc;
    /** @var string[]|null */
    private $nullableArrayOfScalarsWithTypeInPhpDoc;

    private Tag $object;
    private ?Tag $nullableObject;
    #[Schema(nullable: false)]
    private ?Tag $nullableObjectWhenNullableIsFalse;
    /** @var Tag */
    private $objectWithTypeInPhpDoc;
    /** @var Tag|null */
    private $nullableObjectWithTypeInPhpDoc;

    /** @var Tag[] */
    private array $arrayOfObjects;
    /** @var Tag[]|null */
    private ?array $nullableArrayOfObjects;
    /** @var Tag[]|null */
    #[Schema(nullable: false)]
    private ?array $nullableArrayOfObjectsWhenNullableIsFalse;
    /** @var Tag[] */
    private $arrayOfSbjectsWithTypeInPhpDoc;
    /** @var Tag[]|null */
    private $nullableArrayOfObjectsWithTypeInPhpDoc;

    private $emptyType;
    private string|int $unionType;
    private null|string|int $nullableUnionType;
    private array $arrayWithoutType;
    /** */
    private array $arrayWithoutDocType;
    /** @var array */
    private $arrayWithoutTypeAndPlainArrayInPhpDoc;
    /** @var array|null */
    private $nullableArrayWithoutTypeAndPlainArrayInPhpDoc;

    private object $builtinObject;
    private ?object $nullableBuiltinObject;
    #[Schema(nullable: false)]
    private ?object $nullableBuiltinObjectWhenNullableIsFalse;

    private ChildClass $nestedObject;

    private Message $polymorphicObject;
    private ?Message $nullablePolymorphicObjects;
    /** @var Message[] */
    private array $arrayOfPolymorphicObjects;
    /** @var Message[]|null */
    private ?array $nullableArrayOfPolymorphicObjects;

    private BakedIntEnum $intEnum;
    private BakedStringEnum $stringEnum;
    private ?BakedIntEnum $nullableEnum;
    /** @var BakedStringEnum[] */
    private array $arrayOfEnums;
}
