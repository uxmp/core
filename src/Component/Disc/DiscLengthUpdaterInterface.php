<?php

namespace Uxmp\Core\Component\Disc;

use Uxmp\Core\Orm\Model\DiscInterface;

interface DiscLengthUpdaterInterface
{
    public function update(DiscInterface $disc, ): void;
}
