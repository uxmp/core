<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

interface GenreMapInterface
{
    public function getId(): string;

    public function getGenreTitle(): string;

    public function getGenreId(): int;

    public function getMappedItemType(): GenreMapEnum;

    public function setMappedItemType(GenreMapEnum $value): GenreMapInterface;

    public function getMappedItemId(): int;

    public function setMappedItemId(int $mapped_item_id): GenreMapInterface;

    public function setGenre(GenreInterface $genre): GenreMapInterface;

    public function getGenre(): GenreInterface;
}
