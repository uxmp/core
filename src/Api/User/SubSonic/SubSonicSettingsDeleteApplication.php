<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User\SubSonic;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Authentication\AccessKey\AccessTokenEnum;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;

/**
 * Deletes the users subsonic api settings
 */
final class SubSonicSettingsDeleteApplication extends AbstractApiApplication
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

        if ($accessKey !== null) {
            $this->accessKeyRepository->delete($accessKey);
        }

        return $this->asJson(
            $response,
            [
                'accessToken' => null,
            ]
        );
    }
}
