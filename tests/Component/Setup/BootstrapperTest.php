<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\CliInteractorHelper;
use Uxmp\Core\Component\Setup\Validator\Exception\EnvironmentValidationException;
use Uxmp\Core\Component\Setup\Validator\ValidatorInterface;

class BootstrapperTest extends MockeryTestCase
{
    private MockInterface $validator;

    private Bootstrapper $subject;

    public function setUp(): void
    {
        $this->validator = Mockery::mock(ValidatorInterface::class);

        $this->subject = new Bootstrapper(
            [$this->validator]
        );
    }

    public function testBootstrapErrorsOnValidationFailure(): void
    {
        $message = 'some-error';

        $io = Mockery::mock(CliInteractorHelper::class);

        $this->validator->shouldReceive('validate')
            ->withNoArgs()
            ->once()
            ->andThrow(new EnvironmentValidationException($message));

        $io->shouldReceive('error')
            ->with($message, true)
            ->once();

        $this->subject->bootstrap($io);
    }

    public function testBootstrapBootstraps(): void
    {
        $io = Mockery::mock(CliInteractorHelper::class);

        $this->validator->shouldReceive('validate')
            ->withNoArgs()
            ->once();

        $this->subject->bootstrap($io);
    }
}
