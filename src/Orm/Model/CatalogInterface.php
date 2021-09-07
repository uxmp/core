<?php

namespace Uxmp\Core\Orm\Model;

interface CatalogInterface
{
    public function getId(): int;

    public function getPath(): string;

    public function setPath(string $path): CatalogInterface;
}
