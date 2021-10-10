<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery\Adapter\Phpunit\MockeryTestCase;

abstract class ModelTestCase extends MockeryTestCase
{
    abstract public function setterGetterDataProvider(): array;

    protected mixed $subject;

    /**
     * @dataProvider setterGetterDataProvider
     */
    public function testSetterGetter(string $method, mixed $value): void
    {
        $this->assertSame(
            $this->subject,
            $this->subject->{'set'.$method}($value)
        );

        $this->assertSame(
            $value,
            $this->subject->{'get'.$method}()
        );
    }
}
