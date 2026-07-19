<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetPlacemarkerTypes;

use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesFetcherInterface;
use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesHandler;
use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesQuery;
use PHPUnit\Framework\TestCase;

final class GetPlacemarkerTypesHandlerTest extends TestCase
{
    public function testInvokeReturnsFetcherResult(): void
    {
        $query = new GetPlacemarkerTypesQuery();
        $expectedResult = [
            [
                'slug' => 'cafe',
                'name' => 'кафе',
            ],
            [
                'slug' => 'default',
                'name' => 'default',
            ],
        ];

        $fetcher = $this->createMock(GetPlacemarkerTypesFetcherInterface::class);
        $fetcher->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn($expectedResult);

        $handler = new GetPlacemarkerTypesHandler($fetcher);

        $this->assertSame($expectedResult, $handler($query));
    }
}
