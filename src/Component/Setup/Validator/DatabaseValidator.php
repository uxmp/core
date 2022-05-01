<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Throwable;
use Uxmp\Core\Component\Setup\Validator\Exception\EnvironmentValidationException;

final class DatabaseValidator implements ValidatorInterface
{
    public function __construct(
        private readonly ContainerInterface $dic,
    ) {
    }

    public function validate(): void
    {
        $dsn = $_ENV['DATABASE_DSN'] ?? '';

        if ($dsn === '') {
            throw new EnvironmentValidationException(
                'Database DSN is not set in config'
            );
        }

        try {
            $this->dic->get(EntityManagerInterface::class)->getConnection()->connect();
        } catch (Throwable $e) {
            throw new EnvironmentValidationException(
                'Database connection could not be established',
                0,
                $e
            );
        }
    }
}
