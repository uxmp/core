<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * Delivers the user settings
 */
final class UserSettingsRetrieveApplication extends AbstractApiApplication
{
    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        return $this->asJson(
            $response,
            [
                'language' => $user->getLanguage(),
            ]
        );
    }
}
