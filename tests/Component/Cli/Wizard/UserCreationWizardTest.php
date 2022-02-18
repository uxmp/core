<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli\Wizard;

use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\CliInteractorHelper;
use Uxmp\Core\Component\User\UserCreatorInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

class UserCreationWizardTest extends MockeryTestCase
{
    private MockInterface $userRepository;

    private MockInterface $userCreator;

    private UserCreationWizard $subject;

    private MockInterface $interactor;

    public function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userCreator = Mockery::mock(UserCreatorInterface::class);

        $this->subject = new UserCreationWizard(
            $this->userRepository,
            $this->userCreator,
        );

        $this->interactor = Mockery::mock(CliInteractorHelper::class);
    }

    public function testCreateErrorsIfUserAlreadyExists(): void
    {
        $username = 'some-user';

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturn(Mockery::mock(UserInterface::class));

        $this->interactor->shouldReceive('error')
            ->with('A user with that name already exists', true)
            ->once();

        $this->subject->create($this->interactor, $username);
    }

    public function testCreateErrorsIfPasswordDoesNotValidate(): void
    {
        $username = 'some-user';

        $this->expectException(InvalidArgumentException::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturnNull();

        $this->interactor->shouldReceive('promptHidden')
            ->with(
                'Password',
                Mockery::on(function (callable $fn): bool {
                    try {
                        $fn(' ');
                    } catch (InvalidArgumentException) {
                        // pass
                    }
                    return true;
                }),
                2
            )
            ->once()
            ->andThrow(new InvalidArgumentException());

        $this->subject->create($this->interactor, $username);
    }

    public function testCreateErrorsIfFailsOnTooManyPasswordRetries(): void
    {
        $username = 'some-user';

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturnNull();

        $this->interactor->shouldReceive('promptHidden')
            ->with(
                'Password',
                Mockery::on(function (callable $fn): bool {
                    return true;
                }),
                2
            )
            ->once()
            ->andReturn('');
        $this->interactor->shouldReceive('error')
            ->with('Too many retries - aborting', true)
            ->once();

        $this->subject->create($this->interactor, $username);
    }

    public function testCreateCreatesUser(): void
    {
        $username = 'some-user';
        $password = 'password';
        $userId = 666;

        $user = Mockery::mock(UserInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $username])
            ->once()
            ->andReturnNull();

        $this->userCreator->shouldReceive('create')
            ->with($username, $password)
            ->once()
            ->andReturn($user);

        $this->interactor->shouldReceive('promptHidden')
            ->with(
                'Password',
                Mockery::on(function (callable $fn) use ($password): bool {
                    return $fn($password) === $password;
                }),
                2
            )
            ->once()
            ->andReturn($password);
        $this->interactor->shouldReceive('info')
            ->with(
                sprintf(
                    'Created user `%s` with id `%d`',
                    $username,
                    $userId,
                ),
                true
            )
            ->once();

        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);

        $this->subject->create($this->interactor, $username);
    }
}
