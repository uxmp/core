<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class PlaySongApplicationTest extends MockeryTestCase
{
    private MockInterface $psr17Factory;

    private MockInterface $songRepository;

    private PlaySongApplication $subject;

    public function setUp(): void
    {
        $this->psr17Factory = Mockery::mock(Psr17Factory::class);
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);

        $this->subject = new PlaySongApplication(
            $this->psr17Factory,
            $this->songRepository,
        );
    }

    public function testInvokeThrowsIfSongWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $songId = 42;

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('song not found');

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturnNull();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['id' => (string) $songId])
        );
    }

    public function testInvokeReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $songId = 42;
        $fileSize = 666;
        $fileName = 'some-file-name';
        $mimeType = 'some-mime-type';

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturn($song);

        $this->psr17Factory->shouldReceive('createStreamFromFile')
            ->with($fileName)
            ->once()
            ->andReturn($stream);

        $song->shouldReceive('getFilename')
            ->withNoArgs()
            ->once()
            ->andReturn($fileName);
        $song->shouldReceive('getFileSize')
            ->withNoArgs()
            ->once()
            ->andReturn($fileSize);
        $song->shouldReceive('getMimeType')
            ->withNoArgs()
            ->once()
            ->andReturn($mimeType);

        $response->shouldReceive('withHeader')
            ->with('Content-Type', $mimeType)
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Disposition', sprintf('filename=song%d.mp3', $songId))
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Length', (string) $fileSize)
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Cache-Control', 'no-cache')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Range', 'bytes '.$fileSize)
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Accept-Ranges', 'bytes')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withBody')
            ->with($stream)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['id' => (string) $songId])
        );
    }
}
