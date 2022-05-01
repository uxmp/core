<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Catalog\Manage\CatalogUpdaterInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;

final class CatalogUpdateCommand extends Command
{
    public function __construct(
        private readonly ContainerInterface $dic
    ) {
        parent::__construct(
            'catalog:update',
            'Updates the catalog'
        );

        $this
            ->argument(
                '<catalogId>',
                'Id of the catalog'
            )
            ->usage(
                '<bold>  $0 cu 666</end> <comment></end> ## Update the catalog with id `666`<eol/>'
            );
    }

    public function execute(?int $catalogId): void
    {
        $this->dic->get(CatalogUpdaterInterface::class)->update(
            $this->app()?->io(),
            (int) $catalogId
        );

        $this->dic->get(EventHandlerInterface::class)->run();
    }
}
