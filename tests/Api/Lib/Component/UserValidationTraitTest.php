<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Component;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\Lib\Exception\AccessViolation;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Component\User\OwnerProviderInterface;

class UserValidationTraitTest extends MockeryTestCase
{
    private object $subject;

    public function setUp(): void
    {
        $this->subject = new class () {
            use OwnerValidationTrait;

            public function validate(OwnerProviderInterface $provider, ServerRequestInterface $request): void
            {
                $this->validateOwner($request, $provider);
            }
        };
    }

    public function testValidateUserThrowsExceptionIfNotMatching(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $provider = Mockery::mock(OwnerProviderInterface::class);

        $this->expectException(AccessViolation::class);

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER_ID)
            ->once()
            ->andReturn(666);

        $provider->shouldReceive('getOwner->getId')
            ->withNoArgs()
            ->once()
            ->andReturn(42);

        $this->subject->validate($provider, $request);
    }
}
