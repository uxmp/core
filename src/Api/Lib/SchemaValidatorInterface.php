<?php

namespace Uxmp\Core\Api\Lib;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @template TBody
 */
interface SchemaValidatorInterface
{
    /**
     * @return TBody&array
     *
     * @throws Exception\ValidatorException
     */
    public function getValidatedBody(
        ServerRequestInterface $request,
        string $schemaFileName
    ): array;
}
