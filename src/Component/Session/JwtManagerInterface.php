<?php

namespace Uxmp\Core\Component\Session;

interface JwtManagerInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function encode(array $payload): string;
}
