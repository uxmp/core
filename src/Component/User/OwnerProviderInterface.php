<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Uxmp\Core\Orm\Model\UserInterface;

/**
 * Every object which supports the concept of "ownership" implements this interface
 */
interface OwnerProviderInterface
{
    public function getOwner(): UserInterface;
}
