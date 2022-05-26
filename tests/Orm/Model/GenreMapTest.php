<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;

class GenreMapTest extends ModelTestCase
{
    /** @var mixed|GenreMap */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new GenreMap();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['MappedItemType', GenreMapEnum::ALBUM],
            ['MappedItemId', 666],
            ['Genre', Mockery::mock(GenreInterface::class)],
        ];
    }

    public function testGenreGetterMethods(): void
    {
        $genre = Mockery::mock(GenreInterface::class);

        $title = 'some-title';
        $id = 666;

        $genre->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($title);
        $genre->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);

        $this->subject->setGenre($genre);

        $this->assertSame(
            $title,
            $this->subject->getGenreTitle()
        );
        $this->assertSame(
            $id,
            $this->subject->getGenreId()
        );
    }
}
