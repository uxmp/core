<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\Smartlist\Type;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;

class StaticPlaylistTypeTest extends MockeryTestCase
{
    private StaticPlaylistType $subject;

    public function setUp(): void
    {
        $this->subject = new StaticPlaylistType();
    }

    public function testGetSongListReturnsValue(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $songId = 666;

        $playlist->shouldReceive('getSongList')
            ->withNoArgs()
            ->once()
            ->andReturn([$songId]);

        $this->assertSame(
            [$songId],
            $this->subject->getSongList($playlist, $user)
        );
    }
}
