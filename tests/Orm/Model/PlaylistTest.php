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
        ];
    }
}
