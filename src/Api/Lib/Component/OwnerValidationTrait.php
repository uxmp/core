<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Component;

use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\Lib\Exception\AccessViolation;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Component\User\OwnerProviderInterface;

/**
 * Match a user object against the requesting user
 */
trait OwnerValidationTrait
{
    /**
     * @throws AccessViolation
     */
    private function validateOwner(
        ServerRequestInterface $request,
        OwnerProviderInterface $provider
    ): void {
        if ($provider->getOwner()->getId() !== $request->getAttribute(SessionValidatorMiddleware::USER_ID)) {
            throw new AccessViolation();
        }
    }
}
