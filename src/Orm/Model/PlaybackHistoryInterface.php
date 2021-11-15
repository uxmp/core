<?php

namespace Uxmp\Core\Orm\Model;

interface PlaybackHistoryInterface
{
    public function getId(): int;

    public function getUser(): UserInterface;

    public function setUser(UserInterface $user): PlaybackHistoryInterface;

    public function getSong(): SongInterface;

    public function setSong(SongInterface $song): PlaybackHistoryInterface;

    public function getPlayDate(): \DateTimeInterface;

    public function setPlayDate(\DateTimeInterface $play_date): PlaybackHistoryInterface;
}
