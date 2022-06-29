<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage\Update;

use Generator;

/**
 * Recursively iterate over all files in the directory and yield the absolute path of every file
 */
final class RecursiveFileReader implements RecursiveFileReaderInterface
{
    /**
     * @return Generator<string> Absolute path to the file
     */
    public function read(string $directory): Generator
    {
        $files = @scandir($directory);

        if ($files !== false) {
            foreach ($files as $value) {
                $path = (string) realpath($directory . DIRECTORY_SEPARATOR . $value);
                if (!is_dir($path)) {
                    yield $path;
                } elseif ($value !== '.' && $value !== '..') {
                    yield from $this->read($path);
                }
            }
        }
    }
}
