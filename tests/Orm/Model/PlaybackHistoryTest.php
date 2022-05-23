<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use DateTime;
use Mockery;

class PlaybackHistoryTest extends ModelTestCase
{
    /** @var PlaybackHistory */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new PlaybackHistory();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['User', Mockery::mock(UserInterface::class)],
            ['Song', Mockery::mock(SongInterface::class)],
            ['PlayDate', new DateTime()],
        ];
    }
}
