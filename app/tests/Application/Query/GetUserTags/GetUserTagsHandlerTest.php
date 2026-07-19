<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetUserTags;

use App\Application\Query\GetUserTags\GetUserTagsFetcherInterface;
use App\Application\Query\GetUserTags\GetUserTagsHandler;
use App\Application\Query\GetUserTags\GetUserTagsQuery;
use PHPUnit\Framework\TestCase;

final class GetUserTagsHandlerTest extends TestCase
{
    public function testInvokeReturnsFetcherResult(): void
    {
        $query = new GetUserTagsQuery('123e4567-e89b-12d3-a456-426614174000');
        $expectedResult = [
            [
                'id' => 'tag-1',
                'type_id' => 'cafe',
                'name' => 'Food',
                'description' => 'Restaurants',
            ],
        ];

        $fetcher = $this->createMock(GetUserTagsFetcherInterface::class);
        $fetcher->expects($this->once())
            ->method('fetch')
            ->with($query)
            ->willReturn($expectedResult);

        $handler = new GetUserTagsHandler($fetcher);

        $this->assertSame($expectedResult, $handler($query));
    }
}
