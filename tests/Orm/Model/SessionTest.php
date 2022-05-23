<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;

class SessionTest extends ModelTestCase
{
    /** @var mixed|Session */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Session();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Subject', 'some-subject'],
            ['Active', true],
            ['User', Mockery::mock(UserInterface::class)],
        ];
    }
}
