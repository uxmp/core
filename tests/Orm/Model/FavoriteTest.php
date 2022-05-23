<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use DateTime;
use Mockery;

class FavoriteTest extends ModelTestCase
{
    /** @var mixed|Favorite */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Favorite();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['User', Mockery::mock(UserInterface::class)],
            ['Type', 'some-type'],
            ['ItemId', 666],
            ['Date', new DateTime()],
        ];
    }
}
