<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Bootstrap\Init;
use Uxmp\Core\Component\Catalog\Manage\CatalogCleanerInterface;

final class CatalogCleanCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            'catalog:clean',
            'Cleans the catalog'
        );

        $this
            ->argument(
                '<catalogId>',
                'Id of the catalog'
            )
            ->usage(
                '<bold>  $0 cu 666</end> <comment></end> ## Clean the catalog with id `666`<eol/>'
            );
    }

    public function execute(?int $catalogId): void
    {
        Init::run(
            function (ContainerInterface $dic) use ($catalogId): void {
                $dic->get(CatalogCleanerInterface::class)->clean($this->app()?->io(), (int) $catalogId);
            }
        );
    }
}
