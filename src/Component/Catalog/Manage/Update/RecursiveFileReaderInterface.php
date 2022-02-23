<?php

namespace Uxmp\Core\Component\Catalog\Manage\Update;

use Generator;

/**
 * Recursively iterate over all files in the directory and yield the absolute path of every file
 */
interface RecursiveFileReaderInterface
{
    /**
     * @return Generator<string> Absolute path to the file
     */
    public function read(string $directory): Generator;
}
