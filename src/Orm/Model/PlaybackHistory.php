<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepository;

/**
 * Describes the database model which contains the playback history of all users
 */
#[ORM\Entity(repositoryClass: PlaybackHistoryRepository::class)]
#[ORM\Table(name: 'playback_history')]
class PlaybackHistory implements PlaybackHistoryInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $song_id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $user_id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $play_date;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private UserInterface $user;

    #[ORM\ManyToOne(targetEntity: Song::class)]
    #[ORM\JoinColumn(name: 'song_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private SongInterface $song;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): PlaybackHistoryInterface
    {
        $this->user = $user;
        return $this;
    }

    public function getSong(): SongInterface
    {
        return $this->song;
    }

    public function setSong(SongInterface $song): PlaybackHistoryInterface
    {
        $this->song = $song;
        return $this;
    }

    public function getPlayDate(): DateTimeInterface
    {
        return $this->play_date;
    }

    public function setPlayDate(DateTimeInterface $play_date): PlaybackHistoryInterface
    {
        $this->play_date = $play_date;
        return $this;
    }
}
