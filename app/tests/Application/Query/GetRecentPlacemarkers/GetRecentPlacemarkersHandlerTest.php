<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetRecentPlacemarkers;

use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersFetcherInterface;
use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersHandler;
use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersQuery;
use PHPUnit\Framework\TestCase;

final class GetRecentPlacemarkersHandlerTest extends TestCase
{
    public function testInvokeReturnsFetcherResult(): void
    {
        $query = new GetRecentPlacemarkersQuery('123e4567-e89b-12d3-a456-426614174000', 10);
        $expectedResult = [
            [
                'id' => '223e4567-e89b-12d3-a456-426614174000',
                'name' => 'Cafe',
                'lat' => 45.0,
                'lon' => 90.0,
                'type_id' => 'cafe',
                'tags' => ['tag-1'],
                'description' => null,
            ],
        ];

        $fetcher = $this->createMock(GetRecentPlacemarkersFetcherInterface::class);
        $fetcher->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn($expectedResult);

        $handler = new GetRecentPlacemarkersHandler($fetcher);

        $this->assertSame($expectedResult, $handler($query));
    }
}
