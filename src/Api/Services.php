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
        ),
    PlaylistTypes\PlaylistTypesApplication::class => autowire(PlaylistTypes\PlaylistTypesApplication::class)
        ->constructorParameter(
            'playlistTypeList',
            get('playlistTypeHandler')
        ),
    Playlist\PlaylistCreationApplication::class => autowire(Playlist\PlaylistCreationApplication::class)
        ->constructorParameter(
            'playlistTypeList',
            get('playlistTypeHandler')
        ),
];
