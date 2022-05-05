<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Authentication\AccessKey;

enum AccessTokenEnum
{
    public const TYPE_SUBSONIC = 1;

    public const CONFIG_KEY_TOKEN = 'accessToken';

    public const SUBSONIC_KEY_LENGTH = 10;
}
