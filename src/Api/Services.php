<?php

declare(strict_types=1);

namespace Uxmp\Core\Api;

use Opis\JsonSchema\Validator;
use function DI\autowire;
use function DI\get;

return [
    Lib\ResultItemFactoryInterface::class => autowire(Lib\ResultItemFactory::class),
    ApiApplication::class => autowire(),
    Lib\SchemaValidatorInterface::class => autowire(Lib\SchemaValidator::class)
        ->constructorParameter(
            'validator',
            get(Validator::class)
        )
    ,
];
