<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Opis\JsonSchema\Validator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @template TBody
 *
 * @implements SchemaValidatorInterface<TBody>
 */
final class SchemaValidator implements SchemaValidatorInterface
{
    public function __construct(
        private readonly Validator $validator
    ) {
    }

    /**
     * @return TBody&array
     *
     * @throws Exception\ValidatorException
     */
    public function getValidatedBody(
        ServerRequestInterface $request,
        string $schemaFileName
    ): array {
        $schemaFilePath = __DIR__ . '/../../../resource/api-schema/' . $schemaFileName;

        if (!file_exists($schemaFilePath)) {
            throw new Exception\ValidatorException(
                sprintf('Schema `%s` not found', $schemaFileName)
            );
        }

        $body = (string) $request->getBody();

        $bodyDecoded = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception\ValidatorException(
                json_last_error_msg()
            );
        }

        $result = $this->validator->validate(
            $bodyDecoded,
            file_get_contents($schemaFilePath)
        );

        if (!$result->isValid()) {
            throw new Exception\ValidatorException(
                (string) $result->error()?->message()
            );
        }

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }
}
