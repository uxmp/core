<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Setup;

use Ahc\Cli\IO\Interactor;

final class Bootstrapper implements BootstrapperInterface
{
    /**
     * @param array<Validator\ValidatorInterface> $validators
     */
    public function __construct(
        private readonly array $validators
    ) {
    }

    public function bootstrap(
        Interactor $io
    ): void {
        foreach ($this->validators as $validator) {
            try {
                $validator->validate();
            } catch (Validator\Exception\ValidationException $e) {
                $io->error($e->getMessage(), true);

                break;
            }
        }
    }
}
