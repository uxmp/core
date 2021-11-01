<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogListerInterface;

final class CatalogListCommand extends Command
{
    public function __construct(
        private ContainerInterface $dic
    ) {
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
        $this->dic->get(CatalogListerInterface::class)->list($this->app()?->io());
    }
}
