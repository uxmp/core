<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;

class PlaylistTest extends ModelTestCase
{
    /** @var mixed|Playlist */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Playlist();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Name', 'some-name'],
            ['Owner', Mockery::mock(UserInterface::class)],
            ['SongList', [666]],
            ['SongCount', 42],
        ];
    }

    public function testUpdateSongListSetsData(): void
    {
        $list = [42];

        $this->subject->updateSongList($list);

        $this->assertSame(
            $list,
            $this->subject->getSongList()
        );
        $this->assertSame(
            1,
            $this->subject->getSongCount()
        );
    }
}
