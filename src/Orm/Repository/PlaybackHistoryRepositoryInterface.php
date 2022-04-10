<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\SongInterface;

/**
 * @extends ObjectRepository<PlaybackHistoryInterface>
 */
interface PlaybackHistoryRepositoryInterface extends ObjectRepository
{
    public function prototype(): PlaybackHistoryInterface;

    /**
     * @return iterable<PlaybackHistoryInterface>
     */
    public function findBySong(SongInterface $song): iterable;

    /**
     * @param int $number Amount of items to retrieve
     *
     * @return iterable<array{cnt: int, song_id: int}>
     */
    public function getMostPlayed(int $number = 10): iterable;

    public function save(PlaybackHistoryInterface $playbackHistory): void;

    public function delete(PlaybackHistoryInterface $playbackHistory): void;
}
