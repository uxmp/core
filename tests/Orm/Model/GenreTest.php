<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

class GenreTest extends ModelTestCase
{
    /** @var mixed|Genre */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Genre();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Title', 'some-title'],
        ];
    }
}
