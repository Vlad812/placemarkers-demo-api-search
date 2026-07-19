<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\SearchPlacemarkers;

use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersFetcherInterface;
use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersHandler;
use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersQuery;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\SearchArea;
use PHPUnit\Framework\TestCase;

final class SearchPlacemarkersHandlerTest extends TestCase
{
    public function testInvokeReturnsFetcherResult(): void
    {
        $query = new SearchPlacemarkersQuery(
            SearchArea::create(
                Coordinates::fromFloats(45.0, 90.0),
                1000.0
            ),
            '123e4567-e89b-12d3-a456-426614174000',
        );

        $expectedResult = [
            [
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'name' => 'Test Placemarker',
                'lat' => 45.001,
                'lon' => 90.001,
                'description' => 'A test placemarker',
            ]
        ];

        $fetcherMock = $this->createMock(SearchPlacemarkersFetcherInterface::class);
        $fetcherMock->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn($expectedResult);

        $handler = new SearchPlacemarkersHandler($fetcherMock);

        $actualResult = $handler($query);

        $this->assertSame($expectedResult, $actualResult);
    }
}
