<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\Smartlist\Type;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\FavoriteInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;

class FavoriteSongsTypeTest extends MockeryTestCase
{
    private MockInterface $favoriteRepository;

    private FavoriteSongsType $subject;

    public function setUp(): void
    {
        $this->favoriteRepository = Mockery::mock(FavoriteRepositoryInterface::class);

        $this->subject = new FavoriteSongsType(
            $this->favoriteRepository,
        );
    }

    public function testGetSongListYieldsData(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $favorite = Mockery::mock(FavoriteInterface::class);

        $itemId = 666;

        $this->favoriteRepository->shouldReceive('findBy')
            ->with(['user' => $user, 'type' => 'song'])
            ->once()
            ->andReturn([$favorite]);

        $favorite->shouldReceive('getItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);

        $this->assertSame(
            [$itemId],
            iterator_to_array($this->subject->getSongList($playlist, $user))
        );
    }
}
