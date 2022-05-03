<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;

class TemporaryPlaylistTest extends ModelTestCase
{
    /** @var mixed|TemporaryPlaylist */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new TemporaryPlaylist();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Owner', Mockery::mock(UserInterface::class)],
            ['SongList', [666, 42]],
            ['SongCount', 33],
            ['Offset', 21],
        ];
    }

    public function testUpdateSongListUpdates(): void
    {
        $songList = [666, 42];

        $this->subject->updateSongList($songList);

        $this->assertSame(
            2,
            $this->subject->getSongCount()
        );
        $this->assertSame(
            $songList,
            $this->subject->getSongList()
        );
    }
}
