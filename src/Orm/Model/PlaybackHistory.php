<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

/**
 * Describes the database model which contains the playback history of all users
 *
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\PlaybackHistoryRepository")
 * @Table(name="playback_history")
 */
class PlaybackHistory implements PlaybackHistoryInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="integer")
     */
    private int $song_id;

    /**
     * @Column(type="integer")
     */
    private int $user_id;

    /**
     * @Column(type="datetime")
     */
    private \DateTimeInterface $play_date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private UserInterface $user;

    /**
     * @ManyToOne(targetEntity="Song")
     * @JoinColumn(name="song_id", referencedColumnName="id", onDelete="CASCADE")
     */
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

    public function getPlayDate(): \DateTimeInterface
    {
        return $this->play_date;
    }

    public function setPlayDate(\DateTimeInterface $play_date): PlaybackHistoryInterface
    {
        $this->play_date = $play_date;
        return $this;
    }
}
