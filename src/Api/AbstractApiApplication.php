<?php

declare(strict_types=1);

namespace Usox\Core\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractApiApplication
{
    /**
     * @param array<string, mixed> $args
     */
    abstract protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface;

    /**
     * @param array<string, mixed> $args
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        return $this->run($request, $response, $args);
    }

    /**
     * Adds json content type to the response
     */
    protected function asJson(
        ResponseInterface $response
    ): ResponseInterface {
        return $response->withHeader('Content-Type', 'application/json');
    }
}
