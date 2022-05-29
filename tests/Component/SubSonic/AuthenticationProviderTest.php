<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Usox\HyperSonic\Authentication\Exception\AuthenticationFailedException;
use Uxmp\Core\Component\Authentication\AccessKey\AccessKeyTypeEnum;
use Uxmp\Core\Orm\Model\AccessKeyInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

class AuthenticationProviderTest extends MockeryTestCase
{
    private MockInterface $userRepository;

    private MockInterface $accessKeyRepository;

    private AuthenticationProvider $subject;

    public function setUp(): void
    {
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->accessKeyRepository = Mockery::mock(AccessKeyRepositoryInterface::class);

        $this->subject = new AuthenticationProvider(
            $this->userRepository,
            $this->accessKeyRepository,
        );
    }

    public function testAuthByTokenErrorsIfNoUserWasFound(): void
    {
        $userName = 'some-user';
        $token = 'some-token';
        $salt = 'himalaya';

        $this->expectException(AuthenticationFailedException::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $userName])
            ->once()
            ->andReturnNull();

        $this->subject->authByToken($userName, $token, $salt);
    }

    public function testAuthByTokenErrorsIfItemExistsButConfigIsErroneous(): void
    {
        $userName = 'some-user';
        $token = 'some-token';
        $salt = 'himalaya';

        $this->expectException(AuthenticationFailedException::class);

        $user = Mockery::mock(UserInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $userName])
            ->once()
            ->andReturn($user);

        $this->accessKeyRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'type_id' => AccessKeyTypeEnum::SUBSONIC,
                'active' => true,
            ])
            ->once()
            ->andReturn($accessKey);

        $accessKey->shouldReceive('getConfig')
            ->withNoArgs()
            ->once()
            ->andReturn([]);

        $this->subject->authByToken($userName, $token, $salt);
    }

    public function testAuthByTokenAuths(): void
    {
        $userName = 'some-user';
        $tokenRaw = 'some-token-raw';
        $salt = 'himalaya';
        $token = md5($tokenRaw.$salt);

        $user = Mockery::mock(UserInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $userName])
            ->once()
            ->andReturn($user);

        $this->accessKeyRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'type_id' => AccessKeyTypeEnum::SUBSONIC,
                'active' => true,
            ])
            ->once()
            ->andReturn($accessKey);

        $accessKey->shouldReceive('getConfig')
            ->withNoArgs()
            ->once()
            ->andReturn([
                AuthenticationProvider::CONFIG_KEY_TOKEN => $tokenRaw,
            ]);

        $this->subject->authByToken($userName, $token, $salt);
    }

    public function testAuthByPasswordErrorsIfNoUserWasFound(): void
    {
        $userName = 'some-user';
        $password = 'password';

        $this->expectException(AuthenticationFailedException::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $userName])
            ->once()
            ->andReturnNull();

        $this->subject->authByPassword($userName, $password);
    }

    public function testAuthByPasswordsErrorsIfNotMatching(): void
    {
        $userName = 'some-user';
        $password = 'some-password';

        $this->expectException(AuthenticationFailedException::class);

        $user = Mockery::mock(UserInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $userName])
            ->once()
            ->andReturn($user);

        $this->accessKeyRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'type_id' => AccessKeyTypeEnum::SUBSONIC,
                'active' => true,
            ])
            ->once()
            ->andReturn($accessKey);

        $accessKey->shouldReceive('getConfig')
            ->withNoArgs()
            ->once()
            ->andReturn([
                AuthenticationProvider::CONFIG_KEY_TOKEN => 'some-token',
            ]);

        $this->subject->authByPassword($userName, $password);
    }

    public function testAuthByPasswordAuths(): void
    {
        $userName = 'some-user';
        $password = 'some-password';

        $user = Mockery::mock(UserInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $this->userRepository->shouldReceive('findOneBy')
            ->with(['name' => $userName])
            ->once()
            ->andReturn($user);

        $this->accessKeyRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'type_id' => AccessKeyTypeEnum::SUBSONIC,
                'active' => true,
            ])
            ->once()
            ->andReturn($accessKey);

        $accessKey->shouldReceive('getConfig')
            ->withNoArgs()
            ->once()
            ->andReturn([
                AuthenticationProvider::CONFIG_KEY_TOKEN => $password,
            ]);

        $this->subject->authByPassword($userName, $password);
    }
}
