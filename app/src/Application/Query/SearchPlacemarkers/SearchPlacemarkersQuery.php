<?php

declare(strict_types=1);

namespace App\Application\Query\SearchPlacemarkers;

use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\SearchArea;
use Webmozart\Assert\Assert;

final readonly class SearchPlacemarkersQuery
{
    /**
     * @param list<array{type_id: string, tags: list<string>}> $filters
     */
    public function __construct(
        public SearchArea $area,
        public string $userUuid,
        public array $filters = [],
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::keyExists($requestData, 'lat', 'Missing required parameter: lat');
        Assert::keyExists($requestData, 'lon', 'Missing required parameter: lon');
        Assert::keyExists($requestData, 'radius', 'Missing required parameter: radius');
        Assert::uuid($userUuid, 'Authenticated user uuid must be a valid UUID');

        Assert::numeric($requestData['lat'], 'Parameter lat must be numeric');
        Assert::numeric($requestData['lon'], 'Parameter lon must be numeric');
        Assert::numeric($requestData['radius'], 'Parameter radius must be numeric');

        return new self(
            SearchArea::create(
                Coordinates::fromFloats((float) $requestData['lat'], (float) $requestData['lon']),
                (float) $requestData['radius']
            ),
            $userUuid,
            self::normalizeFilters($requestData['filters'] ?? []),
        );
    }

    /**
     * Example $value:
     * [
     *     ['type_id' => 'parking', 'tags' => ['a0f89579-cd7f-4d81-9aee-0512f756957e']],
     *     ['type_id' => 'marketplace', 'tags' => []],
     * ]
     * or [] when no filters are selected.
     *
     * @param list<array{type_id: string, tags: list<string>}> $value
     * @return list<array{type_id: string, tags: list<string>}>
     */
    private static function normalizeFilters(mixed $value): array
    {
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                return [];
            }
            $value = $decoded;
        }

        if (!is_array($value)) {
            return [];
        }

        $filters = [];
        foreach ($value as $index => $item) {
            Assert::isArray($item, sprintf('Filter at index %d must be an object.', $index));

            Assert::keyExists($item, 'type_id', sprintf('Filter at index %d is missing type_id.', $index));
            Assert::stringNotEmpty($item['type_id'], sprintf('Filter at index %d type_id must not be empty.', $index));

            $tags = $item['tags'] ?? [];
            Assert::isArray($tags, sprintf('Filter at index %d tags must be an array.', $index));
            Assert::allStringNotEmpty($tags, sprintf('Filter at index %d tags must be non-empty strings.', $index));

            $filters[] = [
                'type_id' => $item['type_id'],
                'tags' => array_values($tags),
            ];
        }

        return $filters;
    }
}
