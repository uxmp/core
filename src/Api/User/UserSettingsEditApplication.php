<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

/**
 * Update the user settings
 */
final class UserSettingsEditApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{language: string}> $schemaValidator
     */
    public function __construct(
        private readonly SchemaValidatorInterface $schemaValidator,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'UserSettings.json',
        );

        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        $user->setLanguage($body['language']);

        $this->userRepository->save($user);

        return $this->asJson(
            $response,
            [
                'result' => true,
            ]
        );
    }
}
