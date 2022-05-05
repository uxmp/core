<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User\SubSonic;

use PH7\Generator\Password;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Authentication\AccessKey\AccessTokenEnum;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;

/**
 * Creates and delivers the users subsonic api settings
 */
final class SubSonicSettingsCreateApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly AccessKeyRepositoryInterface $accessKeyRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        $accessKey = $this->accessKeyRepository->findOneBy([
            'user' => $user,
            'type_id' => AccessTokenEnum::TYPE_SUBSONIC,
        ]);

        if ($accessKey === null) {
            $accessKey = $this->accessKeyRepository->prototype()
                ->setUser($user)
                ->setActive(true)
                ->setTypeId(AccessTokenEnum::TYPE_SUBSONIC)
                ->setConfig([
                    AccessTokenEnum::CONFIG_KEY_TOKEN => Password::generate(AccessTokenEnum::SUBSONIC_KEY_LENGTH),
                ]);

            $this->accessKeyRepository->save($accessKey);
        }

        return $this->asJson(
            $response,
            [
                'accessToken' => $accessKey->getConfig()[AccessTokenEnum::CONFIG_KEY_TOKEN] ?? null,
            ]
        );
    }
}
