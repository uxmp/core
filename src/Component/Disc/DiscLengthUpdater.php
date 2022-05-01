<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Disc;

use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;

final class DiscLengthUpdater implements DiscLengthUpdaterInterface
{
    public function __construct(
        private readonly DiscRepositoryInterface $discRepository
    ) {
    }

    public function update(
        DiscInterface $disc,
    ): void {
        $length = 0;

        foreach ($disc->getSongs() as $song) {
            $length += $song->getLength();
        }

        $disc->setLength($length);

        $this->discRepository->save($disc);
    }
}
