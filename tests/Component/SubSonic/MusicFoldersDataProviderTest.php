<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;

class MusicFoldersDataProviderTest extends MockeryTestCase
{
    private MockInterface $catalogRepository;

    private MusicFoldersDataProvider $subject;

    public function setUp(): void
    {
        $this->catalogRepository = Mockery::mock(CatalogRepositoryInterface::class);

        $this->subject = new MusicFoldersDataProvider(
            $this->catalogRepository,
        );
    }

    public function testGetMusicFoldersReturnsData(): void
    {
        $catalog = Mockery::mock(CatalogInterface::class);

        $id = 666;

        $catalog->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($id);

        $this->catalogRepository->shouldReceive('findAll')
            ->withNoArgs()
            ->once()
            ->andReturn([$catalog]);

        $this->assertSame(
            [['id' => (string) $id, 'name' => 'Catalog']],
            iterator_to_array($this->subject->getMusicFolders())
        );
    }
}
