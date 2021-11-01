<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Cli;

use Ahc\Cli\Input\Command;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Art\ArtUpdaterInterface;

final class ArtUpdateCommand extends Command
{
    public function __construct(
        private ContainerInterface $dic,
    ) {
        parent::__construct(
            'art:update',
            'Update artwork of known items'
        );

        $this
            ->argument(
                '<catalogId>',
                'Id of the catalog'
            )
            ->usage(
                '<bold>  $0 au 666</end> <comment></end> ## Update the artwork of the catalog with id `666`<eol/>'
            );
    }

    public function execute(?int $catalogId): void
    {
        $this->dic->get(ArtUpdaterInterface::class)->update((int) $catalogId);
    }
}
