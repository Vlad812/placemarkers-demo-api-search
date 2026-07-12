<?php

declare(strict_types=1);

namespace App\Application\Query\SearchPlacemarkers;

use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\SearchArea;
use Webmozart\Assert\Assert;

final readonly class SearchPlacemarkersQuery
{
    /**
     * @param list<string> $tags
     * @param list<string> $types
     */
    public function __construct(
        public SearchArea $area,
        public string $userUuid,
        public array $tags = [],
        public array $types = [],
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

        $tags = self::normalizeStringList($requestData['tags'] ?? []);
        $types = self::normalizeStringList($requestData['types'] ?? []);

        if ($tags !== []) {
            Assert::allString($tags, 'Parameter tags must contain only strings');
            Assert::allNotEmpty($tags, 'Parameter tags must not contain empty values');
        }

        if ($types !== []) {
            Assert::allString($types, 'Parameter types must contain only strings');
            Assert::allNotEmpty($types, 'Parameter types must not contain empty values');
        }

        return new self(
            SearchArea::create(
                Coordinates::fromFloats((float) $requestData['lat'], (float) $requestData['lon']),
                (float) $requestData['radius']
            ),
            $userUuid,
            array_values($tags),
            array_values($types),
        );
    }

    /**
     * @return list<string>
     */
    private static function normalizeStringList(mixed $value): array
    {
        if (!is_array($value)) {
            if (is_string($value) && $value !== '') {
                return [$value];
            }

            return [];
        }

        return array_values(array_filter($value, static fn (mixed $item): bool => is_string($item) && $item !== ''));
    }
}
