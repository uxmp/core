<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Favorite;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class FavoriteManagerTest extends MockeryTestCase
{
    private FavoriteManager $subject;

    public function setUp(): void
    {
        $this->subject = new FavoriteManager();
    }

    public function testAddReturnsFalse(): void
    {
        $obj = \Mockery::mock(FavoriteAbleInterface::class);

        $userId = 666;

        $this->assertFalse(
            $this->subject->add(
                $obj,
                $userId
            )
        );
    }

    public function testRemoveReturnsFalse(): void
    {
        $obj = \Mockery::mock(FavoriteAbleInterface::class);

        $userId = 666;

        $this->assertFalse(
            $this->subject->remove(
                $obj,
                $userId
            )
        );
    }
}
