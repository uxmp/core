<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\Authentication\AuthenticationProviderInterface;
use Usox\HyperSonic\Authentication\Exception\AuthenticationFailedException;
use Uxmp\Core\Component\Authentication\AccessKey\AccessTokenEnum;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

/**
 * Provides auth mechanism for subsonic api auth
 */
final class AuthenticationProvider implements AuthenticationProviderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AccessKeyRepositoryInterface $accessKeyRepository,
    ) {
    }

    /**
     * Preferred way of auth, using a hashed version of the access token
     *
     * @throws AuthenticationFailedException
     */
    public function authByToken(
        string $userName,
        string $token,
        string $salt,
    ): void {
        $accessKeyFromStorage = $this->retrieveToken($userName);

        // use the provided salt to hash the token
        if (
            $accessKeyFromStorage === null ||
            $token !== md5($accessKeyFromStorage.$salt)
        ) {
            throw new AuthenticationFailedException();
        }
    }

    /**
     * Wah! Support for plaintext/hex encoded auth :(
     */
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

        // search for a valid subsonic token
        $accessKey = $this->accessKeyRepository->findOneBy([
            'user' => $user,
            'type_id' => AccessTokenEnum::TYPE_SUBSONIC,
            'active' => true,
        ]);

        return $accessKey?->getConfig()[AccessTokenEnum::CONFIG_KEY_TOKEN] ?? null;
    }
}
