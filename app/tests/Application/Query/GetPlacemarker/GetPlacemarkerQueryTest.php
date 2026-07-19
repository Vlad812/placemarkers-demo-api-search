<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\GetPlacemarker;

use App\Application\Query\GetPlacemarker\GetPlacemarkerQuery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GetPlacemarkerQueryTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';
    private const string PLACEMARKER_ID = '223e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $query = GetPlacemarkerQuery::createFromRawValues(
            ['id' => self::PLACEMARKER_ID],
            self::USER_UUID,
        );

        $this->assertSame(self::PLACEMARKER_ID, $query->id);
        $this->assertSame(self::USER_UUID, $query->userUuid);
    }

    public function testCreateFromRawValuesMissingIdThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: id');

        GetPlacemarkerQuery::createFromRawValues([], self::USER_UUID);
    }

    public function testCreateFromRawValuesInvalidIdThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter id must be a valid UUID');

        GetPlacemarkerQuery::createFromRawValues(['id' => 'not-a-uuid'], self::USER_UUID);
    }

    public function testCreateFromRawValuesInvalidUserUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Authenticated user uuid must be a valid UUID');

        GetPlacemarkerQuery::createFromRawValues(['id' => self::PLACEMARKER_ID], 'not-a-uuid');
    }
}
