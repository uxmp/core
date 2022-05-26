<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

interface GenreInterface
{
    public function getId(): int;

    public function getTitle(): string;

    public function setTitle(string $title): GenreInterface;
}
