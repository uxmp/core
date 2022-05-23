<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\CliInteractorHelper;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;

class CatalogListerTest extends MockeryTestCase
{
    private MockInterface $catalogRepository;

    private CatalogLister $subject;

    public function setUp(): void
    {
        $this->catalogRepository = Mockery::mock(CatalogRepositoryInterface::class);

        $this->subject = new CatalogLister(
            $this->catalogRepository
        );
    }

    public function testListLists(): void
    {
        $io = Mockery::mock(CliInteractorHelper::class);
        $catalog = Mockery::mock(CatalogInterface::class);

        $catalogId = 666;
        $catalogPath = 'some-path';

        $this->catalogRepository->shouldReceive('findAll')
            ->withNoArgs()
            ->once()
            ->andReturn([$catalog]);

        $catalog->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($catalogId);
        $catalog->shouldReceive('getPath')
            ->withNoArgs()
            ->once()
            ->andReturn($catalogPath);

        $io->shouldReceive('table')
            ->with([['id' => $catalogId, 'path' => $catalogPath]])
            ->once();

        $this->subject->list($io);
    }
}
