<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Psr\Container\ContainerInterface;
use Usox\HyperSonic\FeatureSet\V1161\Contract\ArtistListDataProviderInterface;
use Usox\HyperSonic\FeatureSet\V1161\FeatureSetFactory;
use Usox\HyperSonic\HyperSonic;
use Usox\HyperSonic\HyperSonicInterface;
use function DI\autowire;

return [
    PingDataProvider::class => autowire(),
    LicenseDataProvider::class => autowire(),
    ArtistListDataProviderInterface::class => autowire(),
    AuthenticationProvider::class => autowire(),
    ArtistDataProvider::class => autowire(),
    HyperSonicInterface::class => fn (ContainerInterface $c): HyperSonicInterface => HyperSonic::init(
        new FeatureSetFactory(),
        $c->get(AuthenticationProvider::class),
        [
            'ping.view' => fn () => $c->get(PingDataProvider::class),
            'getLicense.view' => fn () => $c->get(LicenseDataProvider::class),
            'getArtists.view' => fn () => $c->get(ArtistListDataProvider::class),
            'getCoverArt.view' => fn () => $c->get(CoverArtDataProvider::class),
            'getArtist.view' => fn () => $c->get(ArtistDataProvider::class),
            'getGenres.view' => fn () => $c->get(GenresDataProvider::class),
        ],
    ),
];
