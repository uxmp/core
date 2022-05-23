<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\CliInteractorHelper;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;

class CatalogAdderTest extends MockeryTestCase
{
    private MockInterface $catalogRepository;

    private CatalogAdder $subject;

    public function setUp(): void
    {
        $this->catalogRepository = Mockery::mock(CatalogRepositoryInterface::class);

        $this->subject = new CatalogAdder(
            $this->catalogRepository
        );
    }

    public function testAddFailsIfPathDoesNotExist(): void
    {
        $io = Mockery::mock(CliInteractorHelper::class);

        $path = 'some-path';

        $io->shouldReceive('error')
            ->with(
                sprintf('`%s` is not a valid path', $path),
                true
            )
            ->once();

        $this->subject->add($io, $path);
    }

    public function testAddFailsIfPathIsAlreadyKnown(): void
    {
        $io = Mockery::mock(CliInteractorHelper::class);
        $catalog = Mockery::mock(CatalogInterface::class);

        $path = '/tmp/';
        $realPath = '/tmp';

        $this->catalogRepository->shouldReceive('findOneBy')
            ->with(['path' => $realPath])
            ->once()
            ->andReturn($catalog);

        $io->shouldReceive('error')
            ->with(
                sprintf('`%s` has already been added as a catalog', $realPath),
                true
            )
            ->once();

        $this->subject->add($io, $path);
    }

    public function testAddAdds(): void
    {
        $io = Mockery::mock(CliInteractorHelper::class);
        $catalog = Mockery::mock(CatalogInterface::class);

        $path = '/tmp/';
        $realPath = '/tmp';
        $catalogId = 666;

        $this->catalogRepository->shouldReceive('findOneBy')
            ->with(['path' => $realPath])
            ->once()
            ->andReturnNull();
        $this->catalogRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($catalog);
        $this->catalogRepository->shouldReceive('save')
            ->with($catalog)
            ->once();

        $catalog->shouldReceive('setPath')
            ->with($realPath)
            ->once();
        $catalog->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($catalogId);

        $io->shouldReceive('ok')
            ->with(
                sprintf('Catalog has been added with id `%s`', $catalogId),
                true
            )
            ->once();

        $this->subject->add($io, $path);
    }
}
