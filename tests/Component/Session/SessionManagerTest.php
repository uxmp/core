<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Session;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\User\PasswordVerificatorInterface;
use Uxmp\Core\Orm\Model\SessionInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SessionRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

class SessionManagerTest extends MockeryTestCase
{
    private MockInterface $sessionRepository;

    private MockInterface $userRepository;

    private MockInterface $passwordVerificator;

    private SessionManager $subject;

    public function setUp(): void
    {
        $this->sessionRepository = Mockery::mock(SessionRepositoryInterface::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->passwordVerificator = Mockery::mock(PasswordVerificatorInterface::class);

        $this->subject = new SessionManager(
            $this->sessionRepository,
            $this->userRepository,
            $this->passwordVerificator,
        );
    }

    public function testLookupReturnsData(): void
    {
        $sessionId = 666;

        $this->sessionRepository->shouldReceive('find')
            ->with($sessionId)
            ->once()
            ->andReturnNull();

        $this->assertNull(
            $this->subject->lookup($sessionId)
        );
    }

    public function testLogoutDoesNothingIfLookupFails(): void
    {
        $sessionId = 666;

        $this->sessionRepository->shouldReceive('find')
            ->with($sessionId)
            ->once()
            ->andReturnNull();

        $this->subject->logout($sessionId);
    }

    public function testLogoutPerformsLogout(): void
    {
        $sessionId = 666;

        $session = Mockery::mock(SessionInterface::class);

        $this->sessionRepository->shouldReceive('find')
            ->with($sessionId)
            ->once()
            ->andReturn($session);
        $this->sessionRepository->shouldReceive('save')
            ->with($session)
            ->once();

        $session->shouldReceive('setActive')
            ->with(false)
            ->once();

        $this->subject->logout($sessionId);
    }

    public function testLoginReturnsNullIfUserWasNotFound(): void
    {
        $username = 'some-username';
        $password = 'some-password';

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturnNull();

        $this->assertNull(
            $this->subject->login($username, $password)
        );
    }

    public function testLoginReturnsNullIfPasswordsDontMatch(): void
    {
        $username = 'some-username';
        $password = 'some-password';

        $user = Mockery::mock(UserInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturn($user);

        $this->passwordVerificator->shouldReceive('verify')
            ->with($user, $password)
            ->once()
            ->andReturnFalse();

        $this->assertNull(
            $this->subject->login($username, $password)
        );
    }

    public function testLoginReturnsCreatedSessionAfterLogin(): void
    {
        $username = 'some-username';
        $password = 'some-password';

        $user = Mockery::mock(UserInterface::class);
        $session = Mockery::mock(SessionInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturn($user);

        $this->passwordVerificator->shouldReceive('verify')
            ->with($user, $password)
            ->once()
            ->andReturnTrue();

        $this->sessionRepository->shouldReceive('prototype->setActive')
            ->with(true)
            ->once()
            ->andReturn($session);
        $this->sessionRepository->shouldReceive('save')
            ->with($session)
            ->once();

        $session->shouldReceive('setUser')
            ->with($user)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $session,
            $this->subject->login($username, $password)
        );
    }
}
