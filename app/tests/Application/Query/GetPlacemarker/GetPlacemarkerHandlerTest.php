<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetPlacemarker;

use App\Application\Query\GetPlacemarker\GetPlacemarkerFetcherInterface;
use App\Application\Query\GetPlacemarker\GetPlacemarkerHandler;
use App\Application\Query\GetPlacemarker\GetPlacemarkerQuery;
use App\Domain\Exception\PlacemarkerNotFoundException;
use PHPUnit\Framework\TestCase;

final class GetPlacemarkerHandlerTest extends TestCase
{
    public function testInvokeReturnsFetcherResult(): void
    {
        $query = new GetPlacemarkerQuery(
            '223e4567-e89b-12d3-a456-426614174000',
            '123e4567-e89b-12d3-a456-426614174000',
        );
        $expectedResult = [
            'id' => $query->id,
            'name' => 'Cafe',
            'lat' => 45.0,
            'lon' => 90.0,
            'type_id' => 'cafe',
            'tags' => ['tag-1'],
            'description' => 'Nice place',
            'created_at' => '2026-07-16 12:00:00',
        ];

        $fetcher = $this->createMock(GetPlacemarkerFetcherInterface::class);
        $fetcher->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn($expectedResult);

        $handler = new GetPlacemarkerHandler($fetcher);

        $this->assertSame($expectedResult, $handler($query));
    }

    public function testInvokeThrowsWhenPlacemarkerNotFound(): void
    {
        $query = new GetPlacemarkerQuery(
            '223e4567-e89b-12d3-a456-426614174000',
            '123e4567-e89b-12d3-a456-426614174000',
        );

        $fetcher = $this->createMock(GetPlacemarkerFetcherInterface::class);
        $fetcher->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn(null);

        $handler = new GetPlacemarkerHandler($fetcher);

        $this->expectException(PlacemarkerNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Placemarker with id "%s" was not found.', $query->id));

        $handler($query);
    }
}
