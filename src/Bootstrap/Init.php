<?php

declare(strict_types=1);

namespace Usox\Core\Bootstrap;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Usox\Core\Component\Event\EventHandlerInterface;
use Usox\Core\Component\Tag\Extractor\ExtractorDeterminator;
use Usox\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Usox\Core\Component\Tag\Extractor\Id3v2Extractor;
use Usox\Core\Component\Tag\Extractor\VorbisExtractor;
use function DI\autowire;

final class Init
{
    /**
     * @param callable(ContainerInterface): mixed $app
     *
     * @return mixed
     */
    public static function run(callable $app)
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(require __DIR__ . '/../Api/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Album/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Catalog/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Event/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Orm/Services.php');
        $builder->addDefinitions([
            Psr17Factory::class => autowire(),
            getID3::class => autowire(),
            ExtractorDeterminatorInterface::class => static function (ContainerInterface $c): ExtractorDeterminatorInterface {
                return new ExtractorDeterminator(
                    [
                        $c->get(Id3v2Extractor::class),
                        $c->get(VorbisExtractor::class),
                    ]
                );
            },
            EntityManagerInterface::class => static function (ContainerInterface $c): EntityManagerInterface {
                $paths = [__DIR__ . '/../Orm/Model/'];
                $isDevMode = true;

                // the connection configuration
                $dbParams = [
                    'url' => 'sqlite:///'. __DIR__ .'/../../dev/db.sqlite',
                ];

                $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                return EntityManager::create($dbParams, $config);
            },
        ]);
        $container = $builder->build();

        $result = $app($container);

        $container->get(EventHandlerInterface::class)->run();

        return $result;
    }
}
