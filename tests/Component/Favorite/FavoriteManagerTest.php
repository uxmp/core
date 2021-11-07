<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Favorite;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\FavoriteInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;

class FavoriteManagerTest extends MockeryTestCase
{
    private MockInterface $favoriteRepository;

    private FavoriteManager $subject;

    public function setUp(): void
    {
        $this->favoriteRepository = Mockery::mock(FavoriteRepositoryInterface::class);

        $this->subject = new FavoriteManager(
            $this->favoriteRepository
        );
    }

    public function testAddReturnsFalseIfAlreadyExists(): void
    {
        $obj = Mockery::mock(FavoriteAbleInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $favorite = Mockery::mock(FavoriteInterface::class);

        $itemId = 666;
        $type = 'some-type';

        $obj->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $obj->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn($type);

        $this->favoriteRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'item_id' => $itemId,
                'type' => $type,
            ])
            ->once()
            ->andReturn($favorite);

        $this->assertFalse(
            $this->subject->add(
                $obj,
                $user
            )
        );
    }

    public function testAddReturnsAddsAndReturnsTrue(): void
    {
        $obj = Mockery::mock(FavoriteAbleInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $favorite = Mockery::mock(FavoriteInterface::class);

        $itemId = 666;
        $type = 'some-type';

        $obj->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $obj->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn($type);

        $this->favoriteRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'item_id' => $itemId,
                'type' => $type,
            ])
            ->once()
            ->andReturnNull();
        $this->favoriteRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($favorite);
        $this->favoriteRepository->shouldReceive('save')
            ->with($favorite)
            ->once();

        $favorite->shouldReceive('setUser')
            ->with($user)
            ->once()
            ->andReturnSelf();
        $favorite->shouldReceive('setType')
            ->with($type)
            ->once()
            ->andReturnSelf();
        $favorite->shouldReceive('setItemId')
            ->with($itemId)
            ->once()
            ->andReturnSelf();
        $favorite->shouldReceive('setDate')
            ->with(Mockery::type(\DateTimeInterface::class))
            ->once()
            ->andReturnSelf();

        $this->assertTrue(
            $this->subject->add(
                $obj,
                $user
            )
        );
    }

    public function testRemoveReturnsFalseIfItemDoesNotExist(): void
    {
        $obj = Mockery::mock(FavoriteAbleInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $itemId = 666;
        $type = 'some-type';

        $obj->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $obj->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn($type);

        $this->favoriteRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'item_id' => $itemId,
                'type' => $type,
            ])
            ->once()
            ->andReturnNull();

        $this->assertFalse(
            $this->subject->remove(
                $obj,
                $user
            )
        );
    }

    public function testRemoveRemovesAndReturnsTrue(): void
    {
        $obj = Mockery::mock(FavoriteAbleInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $favorite = Mockery::mock(FavoriteInterface::class);

        $itemId = 666;
        $type = 'some-type';

        $obj->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $obj->shouldReceive('getType')
            ->withNoArgs()
            ->once()
            ->andReturn($type);

        $this->favoriteRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'item_id' => $itemId,
                'type' => $type,
            ])
            ->once()
            ->andReturn($favorite);
        $this->favoriteRepository->shouldReceive('delete')
            ->with($favorite)
            ->once();

        $this->assertTrue(
            $this->subject->remove(
                $obj,
                $user
            )
        );
    }
}
