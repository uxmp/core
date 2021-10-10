<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

class CatalogTest extends ModelTestCase
{
    /** @var mixed|Catalog */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new Catalog();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Path', 'some-path'],
        ];
    }
}
