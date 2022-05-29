<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Authentication\AccessKey;

enum AccessKeyTypeEnum: int
{
    case NONE = 0;
    case SUBSONIC = 1;
}
