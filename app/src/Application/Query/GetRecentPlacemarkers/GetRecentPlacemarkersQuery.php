<?php

declare(strict_types=1);

namespace App\Application\Query\GetRecentPlacemarkers;

use Webmozart\Assert\Assert;

final readonly class GetRecentPlacemarkersQuery
{
    private const int DEFAULT_LIMIT = 10;

    public function __construct(
        public string $userUuid,
        public int $limit,
    ) {
    }

    /**
     * @param array $requestData
     * @param string $userUuid
     * @return self
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::uuid($userUuid, 'Authenticated user uuid must be a valid UUID');

        $limit = $requestData['limit'] ?? self::DEFAULT_LIMIT;
        Assert::numeric($limit, 'Parameter limit must be numeric');
        Assert::greaterThan((int) $limit, 0, 'Parameter limit must be greater than 0');

        return new self($userUuid, (int) $limit);
    }
}
