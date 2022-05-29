<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Mockery;
use Uxmp\Core\Component\Authentication\AccessKey\AccessKeyTypeEnum;

class AccessKeyTest extends ModelTestCase
{
    /** @var mixed|AccessKey */
    protected mixed $subject;

    public function setUp(): void
    {
        $this->subject = new AccessKey();
    }

    public function setterGetterDataProvider(): array
    {
        return [
            ['TypeId', AccessKeyTypeEnum::SUBSONIC],
            ['Active', true],
            ['User', Mockery::mock(UserInterface::class)],
            ['Config', ['some-confiug']],
        ];
    }
}
