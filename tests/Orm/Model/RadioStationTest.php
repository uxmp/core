<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

class RadioStationTest extends ModelTestCase
{
    /** @var mixed|User */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new RadioStation();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['Name', 'some-name'],
            ['Url', 'some-url'],
        ];
    }
}
