<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib\Exception;

use Throwable;

class AccessViolation extends ApiException
{
    public function __construct(
        string $message = 'Access denied',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
