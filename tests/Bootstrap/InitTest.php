<?php

declare(strict_types=1);

namespace Uxmp\Core\Bootstrap;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class InitTest extends MockeryTestCase
{
    public function testRunRuns(): void
    {
        $foo = 'some-result';

        $this->assertSame(
            $foo,
            Init::run(fn () => $foo),
        );
    }
}
