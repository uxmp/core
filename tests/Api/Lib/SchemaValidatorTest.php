<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use stdClass;

class SchemaValidatorTest extends MockeryTestCase
{
    private MockInterface $validator;

    private SchemaValidator $subject;

    public function setUp(): void
    {
        $this->validator = Mockery::mock(Validator::class);

        $this->subject = new SchemaValidator(
            $this->validator
        );
    }

    public function testGetValidatedBodyThrowsExceptionIfSchemaDoesNotExist(): void
    {
        $this->expectException(Exception\ValidatorException::class);
        $this->expectExceptionMessage('Schema `snafu` not found');

        $this->subject->getValidatedBody(
            Mockery::mock(ServerRequestInterface::class),
            'snafu'
        );
    }

    public function testGetValidatedBodyThrowsExceptionIfBodyDoesNotContainValidJson(): void
    {
        $stream = Mockery::mock(StreamInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);

        $request->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $this->expectException(Exception\ValidatorException::class);
        $this->expectExceptionMessage('Syntax error');

        $this->subject->getValidatedBody(
            $request,
            'RadioStationCreation.json'
        );
    }

    public function testGetValidatedBodyThrowsExceptionIfBodyDoesNotValidate(): void
    {
        $stream = Mockery::mock(StreamInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $result = Mockery::mock(ValidationResult::class);
        $validationError = Mockery::mock(ValidationError::class);

        $data = ['name' => 'some-name', 'url' => 'some-url'];
        $message = 'some-message';

        $stream->shouldReceive('__toString')
            ->withNoArgs()
            ->once()
            ->andReturn(json_encode($data));

        $request->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $this->validator->shouldReceive('validate')
            ->with(
                Mockery::type(stdClass::class),
                file_get_contents(__DIR__ . '/../../../resource/api-schema/RadioStationCreation.json')
            )
            ->once()
            ->andReturn($result);

        $result->shouldReceive('isValid')
            ->withNoArgs()
            ->once()
            ->andReturnFalse();
        $result->shouldReceive('error')
            ->withNoArgs()
            ->once()
            ->andReturn($validationError);

        $validationError->shouldReceive('message')
            ->withNoArgs()
            ->once()
            ->andReturn($message);

        $this->expectException(Exception\ValidatorException::class);
        $this->expectExceptionMessage($message);

        $this->subject->getValidatedBody(
            $request,
            'RadioStationCreation.json'
        );
    }

    public function testGetValidatedBodyReturnsBody(): void
    {
        $stream = Mockery::mock(StreamInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $result = Mockery::mock(ValidationResult::class);

        $data = ['name' => 'some-name', 'url' => 'some-url'];

        $stream->shouldReceive('__toString')
            ->withNoArgs()
            ->once()
            ->andReturn(json_encode($data));

        $request->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $this->validator->shouldReceive('validate')
            ->with(
                Mockery::type(stdClass::class),
                file_get_contents(__DIR__ . '/../../../resource/api-schema/RadioStationCreation.json')
            )
            ->once()
            ->andReturn($result);

        $result->shouldReceive('isValid')
            ->withNoArgs()
            ->once()
            ->andReturnTrue();

        $this->assertSame(
            $data,
            $this->subject->getValidatedBody(
                $request,
                'RadioStationCreation.json'
            )
        );
    }
}
