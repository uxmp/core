<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Genre;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

final class GenreListApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly GenreRepositoryInterface $genreRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        foreach ($this->genreRepository->getGenreStatistics() as $statistic) {
            $list[] = [
                'id' => $statistic['id'],
                'name' => $statistic['value'],
                'albumCount' => $statistic['albumCount'],
                'songCount' => $statistic['songCount'],
            ];
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
