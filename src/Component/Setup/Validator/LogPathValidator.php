<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup\Validator;

use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Setup\Validator\Exception\EnvironmentValidationException;

final class LogPathValidator implements ValidatorInterface
{
    public function __construct(
        private readonly ConfigProviderInterface $configProvider,
    ) {
    }

    public function validate(): void
    {
        $logPath = $this->configProvider->getLogFilePath();
        if ($logPath === '') {
            throw new EnvironmentValidationException(
                'LOG_PATH is not set in config'
            );
        }

        if (!is_writeable($logPath)) {
            throw new EnvironmentValidationException(
                sprintf(
                    'LOG_PATH `%s` is not a valid writeable directory',
                    $logPath
                )
            );
        }
    }
}
