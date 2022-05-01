<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogAdderInterface;

final class CatalogAddCommand extends Command
{
    public function __construct(
        private readonly ContainerInterface $dic
    ) {
        parent::__construct(
            'catalog:add',
            'Adds a folder as catalog'
        );

        $this
            ->argument(
                '<path>',
                'Absolute folder path'
            )
            ->usage(
                '<bold>  $0 ca /path/to/files</end> <comment></end> ## Scan<eol/>'
            );
    }

    public function execute(?string $path): void
    {
        $this->dic->get(CatalogAdderInterface::class)->add($this->app()?->io(), (string) $path);
    }
}
