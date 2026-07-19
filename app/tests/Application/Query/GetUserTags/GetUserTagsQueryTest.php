<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetUserTags;

use App\Application\Query\GetUserTags\GetUserTagsQuery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GetUserTagsQueryTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testConstructorStoresUserUuid(): void
    {
        $query = new GetUserTagsQuery(self::USER_UUID);

        $this->assertSame(self::USER_UUID, $query->userUuid);
    }

    public function testCreateFromRawValuesSuccess(): void
    {
        $query = GetUserTagsQuery::createFromRawValues([], self::USER_UUID);

        $this->assertSame(self::USER_UUID, $query->userUuid);
    }

    public function testCreateFromRawValuesInvalidUserUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Authenticated user uuid must be a valid UUID');

        GetUserTagsQuery::createFromRawValues([], 'not-a-uuid');
    }
}
