<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Bootstrap\Init;
use Uxmp\Core\Component\Catalog\Manage\CatalogListerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogUpdaterInterface;

final class CatalogListCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            'catalog:list',
            'Lists all catalogs'
        );

        $this
            ->usage(
                '<bold>  $0 cl</end> <comment></end> ## Show all catalogs<eol/>'
            );
    }

    public function execute(): void
    {
        Init::run(
            function (ContainerInterface $dic): void {
                $dic->get(CatalogListerInterface::class)->list($this->app()?->io());
            }
        );
    }
}
