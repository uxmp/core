<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

class UserSettingsEditApplicationTest extends MockeryTestCase
{
    private MockInterface $userRepository;

    private MockInterface $schemaValidator;

    private UserSettingsEditApplication $subject;

    public function setUp(): void
    {
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);

        $this->subject = new UserSettingsEditApplication(
            $this->schemaValidator,
            $this->userRepository,
        );
    }

    public function testRunEdits(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $language = 'en';

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with(
                $request,
                'UserSettings.json',
            )
            ->once()
            ->andReturn(['language' => $language]);

        $this->userRepository->shouldReceive('save')
            ->with($user)
            ->once();

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $user->shouldReceive('setLanguage')
            ->with($language)
            ->once();

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(['result' => true], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
