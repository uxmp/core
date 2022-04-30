<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\Authentication\AuthenticationProviderInterface;
use Usox\HyperSonic\Authentication\Exception\AuthenticationFailedException;
use Uxmp\Core\Component\Authentication\AccessKey\AccessTokenEnum;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

final class AuthenticationProvider implements AuthenticationProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AccessKeyRepositoryInterface $accessKeyRepository,
    ) {
    }

    public function authByToken(
        string $userName,
        string $token,
        string $salt,
    ): void {
        $accessKeyFromStorage = $this->retrieveToken($userName);

        if (
            $accessKeyFromStorage === null ||
            $token !== md5($accessKeyFromStorage.$salt)
        ) {
            throw new AuthenticationFailedException();
        }
    }

    public function authByPassword(
        string $userName,
        string $password,
    ): void {
        $accessKeyFromStorage = $this->retrieveToken($userName);

        if (
            $accessKeyFromStorage === null ||
            $password !== $accessKeyFromStorage
        ) {
            throw new AuthenticationFailedException();
        }
    }

    private function retrieveToken(
        string $userName,
    ): ?string {
        $user = $this->userRepository->findOneBy(['name' => $userName]);
        if ($user === null) {
            return null;
        }

        $accessKey = $this->accessKeyRepository->findOneBy([
            'user' => $user,
            'type_id' => AccessTokenEnum::TYPE_SUBSONIC,
            'active' => true,
        ]);

        return $accessKey?->getConfig()[AccessTokenEnum::CONFIG_KEY_TOKEN] ?? null;
    }
}
