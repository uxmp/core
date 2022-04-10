<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use JsonSerializable;
use Uxmp\Core\Orm\Model\PlaylistInterface;

/**
 * Generic playlist result item
 */
final class PlaylistItem implements JsonSerializable
{
    public function __construct(
        private PlaylistInterface $playlist
    ) {
    }

    /**
     * @return array{
     *  id: integer,
     *  name: string,
     *  song_count: integer,
     *  user_name: string,
     *  user_id: integer
     * }
     */
    public function jsonSerialize(): array
    {
        $owner = $this->playlist->getOwner();

        return [
            'id' => $this->playlist->getId(),
            'name' => $this->playlist->getName(),
            'song_count' => $this->playlist->getSongCount(),
            'user_name' => $owner->getName(),
            'user_id' => $owner->getId(),
        ];
    }
}
