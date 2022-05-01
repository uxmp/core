<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli\Wizard;

use Ahc\Cli\IO\Interactor;
use InvalidArgumentException;
use Uxmp\Core\Component\User\PasswordVerificator;
use Uxmp\Core\Component\User\UserCreatorInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;
use function mb_strlen;

/**
 * Leads the user through the user creation process on the cli
 */
final class UserCreationWizard implements UserCreationWizardInterface
{
    private const PASSWORD_RETRIES = 2;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserCreatorInterface $userCreator,
    ) {
    }

    public function create(
        Interactor $io,
        string $username,
    ): void {
        $user = $this->userRepository->findOneBy([
            'name' => $username,
        ]);

        if ($user !== null) {
            $io->error('A user with that name already exists', true);
            return;
        }

        $validator = function ($password): string {
            $password = trim((string) $password);
            if (mb_strlen($password) < PasswordVerificator::PASSWORD_MIN_LENGTH) {
                throw new InvalidArgumentException('Password too short');
            }

            return $password;
        };

        $password = $io->promptHidden('Password', $validator, self::PASSWORD_RETRIES);

        if ($password === '') {
            $io->error('Too many retries - aborting', true);
            return;
        }

        $user = $this->userCreator->create(
            $username,
            $password
        );

        $io->info(
            sprintf(
                'Created user `%s` with id `%d`',
                $username,
                $user->getId()
            ),
            true
        );
    }
}
