<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\PlaylistTypes;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Playlist\Smartlist\Type\SmartlistTypeInterface;

/**
 * Playlist type retrieval
 */
final class PlaylistTypesApplication extends AbstractApiApplication
{
    /**
     * @param array<SmartlistTypeInterface> $playlistTypeList
     */
    public function __construct(
        private array $playlistTypeList,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        return $this->asJson(
            $response,
            [
                'items' => array_keys($this->playlistTypeList),
            ]
        );
    }
}
