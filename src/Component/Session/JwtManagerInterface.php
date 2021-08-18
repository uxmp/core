<?php

namespace Uxmp\Core\Component\Session;

interface JwtManagerInterface
{
    public function encode(array $payload): string;
}
