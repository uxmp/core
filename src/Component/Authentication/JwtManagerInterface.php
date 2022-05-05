<?php

namespace Uxmp\Core\Component\Authentication;

interface JwtManagerInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function encode(array $payload): string;
}
