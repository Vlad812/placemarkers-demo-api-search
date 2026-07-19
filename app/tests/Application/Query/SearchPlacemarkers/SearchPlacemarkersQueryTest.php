<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\SearchPlacemarkers;

use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersQuery;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SearchPlacemarkersQueryTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $requestData = [
            'lat' => '45.0',
            'lon' => 90.0,
            'radius' => '1500.5',
        ];

        $query = SearchPlacemarkersQuery::createFromRawValues($requestData, self::USER_UUID);

        $this->assertSame(45.0, $query->area->center()->latitude());
        $this->assertSame(90.0, $query->area->center()->longitude());
        $this->assertSame(1500.5, $query->area->radiusMeters());
        $this->assertSame(self::USER_UUID, $query->userUuid);
        $this->assertSame([], $query->filters);
    }

    public function testCreateFromRawValuesParsesFilters(): void
    {
        $query = SearchPlacemarkersQuery::createFromRawValues([
            'lat' => 45.0,
            'lon' => 90.0,
            'radius' => 1000,
            'filters' => [
                ['type_id' => 'museum', 'tags' => ['tag-1', 'tag-2']],
                ['type_id' => 'marketplace', 'tags' => []],
            ],
        ], self::USER_UUID);

        $this->assertSame([
            ['type_id' => 'museum', 'tags' => ['tag-1', 'tag-2']],
            ['type_id' => 'marketplace', 'tags' => []],
        ], $query->filters);
    }

    public function testCreateFromRawValuesParsesJsonFiltersString(): void
    {
        $query = SearchPlacemarkersQuery::createFromRawValues([
            'lat' => 45.0,
            'lon' => 90.0,
            'radius' => 1000,
            'filters' => '[{"type_id":"cafe","tags":["tag-1"]}]',
        ], self::USER_UUID);

        $this->assertSame([
            ['type_id' => 'cafe', 'tags' => ['tag-1']],
        ], $query->filters);
    }

    #[DataProvider('invalidFiltersProvider')]
    public function testCreateFromRawValuesInvalidFiltersThrowsException(mixed $filters, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        SearchPlacemarkersQuery::createFromRawValues([
            'lat' => 45.0,
            'lon' => 90.0,
            'radius' => 1000,
            'filters' => $filters,
        ], self::USER_UUID);
    }

    public static function invalidFiltersProvider(): array
    {
        return [
            'filter item not array' => [
                ['not-an-object'],
                'Filter at index 0 must be an object.',
            ],
            'missing type_id' => [
                [['tags' => ['tag-1']]],
                'Filter at index 0 is missing type_id.',
            ],
            'empty type_id' => [
                [['type_id' => '', 'tags' => []]],
                'Filter at index 0 type_id must not be empty.',
            ],
            'tags not array' => [
                [['type_id' => 'cafe', 'tags' => 'tag-1']],
                'Filter at index 0 tags must be an array.',
            ],
            'empty tag string' => [
                [['type_id' => 'cafe', 'tags' => ['']]],
                'Filter at index 0 tags must be non-empty strings.',
            ],
        ];
    }

    public function testCreateFromRawValuesInvalidJsonFiltersReturnsEmpty(): void
    {
        $query = SearchPlacemarkersQuery::createFromRawValues([
            'lat' => 45.0,
            'lon' => 90.0,
            'radius' => 1000,
            'filters' => '{not-json}',
        ], self::USER_UUID);

        $this->assertSame([], $query->filters);
    }

    #[DataProvider('missingKeysProvider')]
    public function testCreateFromRawValuesMissingKeysThrowsException(array $requestData, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        SearchPlacemarkersQuery::createFromRawValues($requestData, self::USER_UUID);
    }

    public static function missingKeysProvider(): array
    {
        return [
            'missing lat' => [
                ['lon' => 90.0, 'radius' => 1000],
                'Missing required parameter: lat',
            ],
            'missing lon' => [
                ['lat' => 45.0, 'radius' => 1000],
                'Missing required parameter: lon',
            ],
            'missing radius' => [
                ['lat' => 45.0, 'lon' => 90.0],
                'Missing required parameter: radius',
            ],
        ];
    }

    #[DataProvider('nonNumericValuesProvider')]
    public function testCreateFromRawValuesNonNumericThrowsException(array $requestData, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        SearchPlacemarkersQuery::createFromRawValues($requestData, self::USER_UUID);
    }

    public static function nonNumericValuesProvider(): array
    {
        return [
            'non numeric lat' => [
                ['lat' => 'abc', 'lon' => 90.0, 'radius' => 1000],
                'Parameter lat must be numeric',
            ],
            'non numeric lon' => [
                ['lat' => 45.0, 'lon' => 'def', 'radius' => 1000],
                'Parameter lon must be numeric',
            ],
            'non numeric radius' => [
                ['lat' => 45.0, 'lon' => 90.0, 'radius' => 'xyz'],
                'Parameter radius must be numeric',
            ],
        ];
    }

    public function testCreateFromRawValuesInvalidUserUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Authenticated user uuid must be a valid UUID');

        SearchPlacemarkersQuery::createFromRawValues(
            ['lat' => 45.0, 'lon' => 90.0, 'radius' => 1000],
            'not-a-uuid',
        );
    }
}
