<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;

class ResultItemFactoryTest extends MockeryTestCase
{
    private MockInterface $config;

    private ResultItemFactory $subject;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new ResultItemFactory(
            $this->config
        );
    }

    public function testCreateSongListItemReturnsInstance(): void
    {
        $this->assertInstanceOf(
            SongListItem::class,
            $this->subject->createSongListItem(
                Mockery::mock(SongInterface::class),
                Mockery::mock(AlbumInterface::class),
            )
        );
    }

    public function testCreatePlaybackHistoryItemReturnsInstance(): void
    {
        $this->assertInstanceOf(
            PlaybackHistoryItem::class,
            $this->subject->createPlaybackHistoryItem(
                Mockery::mock(PlaybackHistoryInterface::class),
            )
        );
    }

    public function testCreatePlaylistItemReturnsInstance(): void
    {
        $this->assertInstanceOf(
            PlaylistItem::class,
            $this->subject->createPlaylistItem(
                Mockery::mock(PlaylistInterface::class)
            )
        );
    }
}
