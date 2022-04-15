<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;

final class Init
{
    /**
     * @param callable(ContainerInterface):mixed $app
     */
    public static function run(callable $app): mixed
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(require __DIR__ . '/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Api/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Album/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Catalog/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Event/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Config/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Session/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Song/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Disc/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Artist/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Art/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Setup/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Tag/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Favorite/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Cli/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/User/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Playlist/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/SubSonic/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Component/Authentication/Services.php');
        $builder->addDefinitions(require __DIR__ . '/../Orm/Services.php');

        /** @var ContainerInterface $container */
        $container = $builder->build();

        // @todo validate env variable
        $container->get(Dotenv::class);

        $result = $app($container);

        $container->get(EventHandlerInterface::class)->run();

        return $result;
    }
}
