<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

class PasswordVerificatorTest extends MockeryTestCase
{
    private MockInterface $userRepository;

    private string $defaultAlgo = PASSWORD_DEFAULT;

    private array $options = ['cost' => 11];

    private PasswordVerificator $subject;

    public function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);

        $this->subject = new PasswordVerificator(
            $this->userRepository,
            $this->defaultAlgo,
            $this->options,
        );
    }

    public function testVerifyReturnsFalseIfPasswordsDontMatch(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $user->shouldReceive('getPassword')
            ->withNoArgs()
            ->once()
            ->andReturn('some-snusnu');

        $this->assertFalse(
            $this->subject->verify($user, 'some-password')
        );
    }

    public function testVerifyReturnsTrueOnMatch(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $password = 'some-password';

        $user->shouldReceive('getPassword')
            ->withNoArgs()
            ->once()
            ->andReturn(password_hash($password, $this->defaultAlgo, $this->options));

        $this->assertTrue(
            $this->subject->verify($user, $password)
        );
    }

    public function testVerifyReturnsTrueOnMatchAndUpdatesLegacyHash(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $password = 'some-password';

        $user->shouldReceive('getPassword')
            ->withNoArgs()
            ->once()
            ->andReturn(password_hash($password, $this->defaultAlgo, ['cost' => 10]));
        $user->shouldReceive('setPassword')
            ->with(
                Mockery::type('string')
            )
            ->once();

        $this->userRepository->shouldReceive('save')
            ->with($user)
            ->once();

        $this->assertTrue(
            $this->subject->verify($user, $password)
        );
    }

    public function testHashReturnsHashedPassword(): void
    {
        $password = '12345';

        $this->assertTrue(
            password_verify(
                $password,
                $this->subject->hash($password)
            )
        );
    }
}
