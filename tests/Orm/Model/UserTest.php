<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

class UserTest extends ModelTestCase
{
    /** @var mixed|User */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new User();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Name', 'some-name'],
            ['Password', 'some-password'],
            ['Language', 'some-language'],
        ];
    }
}
