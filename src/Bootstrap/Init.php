<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use getID3;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;
use Uxmp\Core\Component\Tag\Extractor\ExtractorDeterminator;
use Uxmp\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Uxmp\Core\Component\Tag\Extractor\Id3v2Extractor;
use Uxmp\Core\Component\Tag\Extractor\VorbisExtractor;
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
        $builder->addDefinitions(require __DIR__ . '/../Component/Config/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Session/Services.php');
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

                /** @var ConfigProviderInterface $config */
                $config = $c->get(ConfigProviderInterface::class);

                // the connection configuration
                $dbParams = [
                    'url' => $_ENV['DATABASE_DSN'],
                    'password' => $_ENV['DATABASE_PASSWORD'],
                ];

                $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                return EntityManager::create($dbParams, $config);
            },
            Dotenv::class => function (): Dotenv {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
                $dotenv->load();
                return $dotenv;
            },
        ]);
        $container = $builder->build();

        // @todo validate env variable
        $container->get(Dotenv::class);

        $result = $app($container);

        $container->get(EventHandlerInterface::class)->run();

        return $result;
    }
}
