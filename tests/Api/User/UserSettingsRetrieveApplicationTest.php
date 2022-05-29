<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;

class UserSettingsRetrieveApplicationTest extends MockeryTestCase
{
    private UserSettingsRetrieveApplication $subject;

    public function setUp(): void
    {
        $this->subject = new UserSettingsRetrieveApplication();
    }

    public function testRunRetrieveSettings(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $language = 'en';

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['language' => $language], JSON_PRETTY_PRINT))
            ->once();

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $user->shouldReceive('getLanguage')
            ->withNoArgs()
            ->once()
            ->andReturn($language);

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                [
                    'language' => $language,
                ]
            )
        );
    }
}
