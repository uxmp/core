<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Playlist\MediaAddition\Handler\HandlerInterface;
use Uxmp\Core\Component\Playlist\MediaAddition\Handler\HandlerTypeEnum;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistMediaAdderTest extends MockeryTestCase
{
    private MockInterface $handler;

    private MockInterface $playlistRepository;

    private PlaylistMediaAdder $subject;

    public function setUp(): void
    {
        $this->handler = Mockery::mock(HandlerInterface::class);
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);

        $this->subject = new PlaylistMediaAdder(
            [HandlerTypeEnum::SONG->value => $this->handler],
            $this->playlistRepository
        );
    }

    public function testAddErrorsOnUnknownMediaType(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);

        $this->expectException(Exception\InvalidMediaTypeException::class);
        $this->expectExceptionMessage('snafu');

        $this->subject->add(
            $playlist,
            'snafu',
            666
        );
    }

    public function testAddAddsMedia(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);

        $mediaId = 666;
        $existingSongId = 42;

        $this->handler->shouldReceive('handle')
            ->with($mediaId, Mockery::on(function (array &$songList) use ($mediaId): bool {
                $songList[] = $mediaId;

                return true;
            }))
            ->once();

        $playlist->shouldReceive('getSongList')
            ->withNoArgs()
            ->once()
            ->andReturn([$existingSongId]);
        $playlist->shouldReceive('updateSongList')
            ->with([$existingSongId, $mediaId])
            ->once();

        $this->playlistRepository->shouldReceive('save')
            ->with($playlist)
            ->once();

        $this->subject->add(
            $playlist,
            HandlerTypeEnum::SONG->value,
            $mediaId
        );
    }
}
