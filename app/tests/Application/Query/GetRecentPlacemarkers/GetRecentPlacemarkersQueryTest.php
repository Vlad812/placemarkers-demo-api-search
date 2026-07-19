<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetRecentPlacemarkers;

use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersQuery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GetRecentPlacemarkersQueryTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesUsesDefaultLimit(): void
    {
        $query = GetRecentPlacemarkersQuery::createFromRawValues([], self::USER_UUID);

        $this->assertSame(self::USER_UUID, $query->userUuid);
        $this->assertSame(10, $query->limit);
    }

    public function testCreateFromRawValuesParsesLimit(): void
    {
        $query = GetRecentPlacemarkersQuery::createFromRawValues(
            ['limit' => '25'],
            self::USER_UUID,
        );

        $this->assertSame(25, $query->limit);
    }

    public function testCreateFromRawValuesNonPositiveLimitThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter limit must be greater than 0');

        GetRecentPlacemarkersQuery::createFromRawValues(['limit' => 0], self::USER_UUID);
    }

    public function testCreateFromRawValuesNonNumericLimitThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter limit must be numeric');

        GetRecentPlacemarkersQuery::createFromRawValues(['limit' => 'abc'], self::USER_UUID);
    }

    public function testCreateFromRawValuesInvalidUserUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Authenticated user uuid must be a valid UUID');

        GetRecentPlacemarkersQuery::createFromRawValues([], 'not-a-uuid');
    }
}
