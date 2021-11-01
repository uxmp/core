<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup\Validator;

use Uxmp\Core\Component\Setup\Validator\Exception\EnvironmentValidationException;

final class AssetPathValidator implements ValidatorInterface
{
    private const ASSET_SUB_FOLDERS = ['img/album', 'img/artist'];

    private const PERMISSIONS = 0766;

    public function validate(): void
    {
        $assetPath = $_ENV['ASSET_PATH'] ?? '';
        if ($assetPath === '') {
            throw new EnvironmentValidationException(
                'ASSET_PATH is not set in config'
            );
        }

        if (!is_writeable($assetPath)) {
            throw new EnvironmentValidationException(
                sprintf(
                    'ASSET_PATH `%s` is not a valid writeable directory',
                    $assetPath
                )
            );
        }

        foreach (static::ASSET_SUB_FOLDERS as $folder) {
            $path = sprintf('%s/%s', $assetPath, $folder);

            if (!is_dir($path)) {
                $result = mkdir(
                    $path,
                    static::PERMISSIONS,
                    true
                );

                if ($result === false) {
                    throw new EnvironmentValidationException(
                        sprintf('Creation of folder `%s` failed', $path)
                    );
                }
            }
        }
    }
}
