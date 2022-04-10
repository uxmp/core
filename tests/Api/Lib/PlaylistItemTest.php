<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;

class PlaylistItemTest extends MockeryTestCase
{
    private MockInterface $playlist;

    private PlaylistItem $subject;

    public function setUp(): void
    {
        $this->playlist = Mockery::mock(PlaylistInterface::class);

        $this->subject = new PlaylistItem(
            $this->playlist,
        );
    }

    public function testJsonSerializeReturnsData(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $id = 666;
        $name = 'some-name';
        $userId = 42;
        $userName = 'some-username';
        $songCount = 33;

        $this->playlist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);
        $this->playlist->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($name);
        $this->playlist->shouldReceive('getOwner')
            ->withNoArgs()
            ->once()
            ->andReturn($user);
        $this->playlist->shouldReceive('getSongCount')
            ->withNoArgs()
            ->once()
            ->andReturn($songCount);

        $user->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($userName);
        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);

        $this->assertSame(
            [
                'id' => $id,
                'name' => $name,
                'song_count' => $songCount,
                'user_name' => $userName,
                'user_id' => $userId,
            ],
            $this->subject->jsonSerialize()
        );
    }
}
