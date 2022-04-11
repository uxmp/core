<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Playlist\Exception\InvalidPlaylistTypeException;
use Uxmp\Core\Component\Playlist\Smartlist\Type\SmartlistTypeInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class PlaylistSongRetrieverTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private PlaylistSongRetriever $subject;

    private MockInterface $handlerType;

    private int $handlerTypeId = 666;

    public function setUp(): void
    {
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);
        $this->handlerType = Mockery::mock(SmartlistTypeInterface::class);

        $this->subject = new PlaylistSongRetriever(
            $this->songRepository,
            [$this->handlerTypeId => $this->handlerType],
        );
    }

    public function testRetrieveErrorsOnInvalidType(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $this->expectException(InvalidPlaylistTypeException::class);

        $playlist->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn(42);

        iterator_to_array($this->subject->retrieve($playlist, $user));
    }

    public function testRetrieveYieldsSongs(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $songId = 666;
        $missingSongId = 42;

        $this->handlerType->shouldReceive('getSongList')
            ->with($playlist, $user)
            ->once()
            ->andReturn([$songId, $missingSongId]);

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturn($song);
        $this->songRepository->shouldReceive('find')
            ->with($missingSongId)
            ->once()
            ->andReturnNull();

        $playlist->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn($this->handlerTypeId);

        $this->assertSame(
            [$song],
            iterator_to_array($this->subject->retrieve($playlist, $user))
        );
    }
}
