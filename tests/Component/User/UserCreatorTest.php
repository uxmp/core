<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

class UserCreatorTest extends MockeryTestCase
{
    private MockInterface $userRepository;

    private MockInterface $passwordVerificator;

    private UserCreator $subject;

    public function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->passwordVerificator = Mockery::mock(PasswordVerificatorInterface::class);

        $this->subject = new UserCreator(
            $this->userRepository,
            $this->passwordVerificator,
        );
    }

    public function testCreateCreatesAUser(): void
    {
        $username = 'some-name';
        $password = 'some-password';
        $passwordHash = 'some-password-hash';

        $user = Mockery::mock(UserInterface::class);

        $this->userRepository->shouldReceive('prototype->setName')
            ->with($username)
            ->once()
            ->andReturn($user);
        $this->userRepository->shouldReceive('save')
            ->with($user)
            ->once();

        $user->shouldReceive('setPassword')
            ->with($passwordHash)
            ->once()
            ->andReturnSelf();

        $this->passwordVerificator->shouldReceive('hash')
            ->with($password)
            ->once()
            ->andReturn($passwordHash);

        $this->assertSame(
            $user,
            $this->subject->create($username, $password)
        );
    }
}
