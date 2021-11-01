<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup\Validator;

interface ValidatorInterface
{
    /**
     * @throws Exception\ValidationException
     */
    public function validate(): void;
}
