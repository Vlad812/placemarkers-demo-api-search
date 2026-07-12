<?php

declare(strict_types=1);

namespace App\Application\Query\GetPlacemarker;

use Webmozart\Assert\Assert;

final readonly class GetPlacemarkerQuery
{
    public function __construct(
        public string $id,
        public string $userUuid,
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::keyExists($requestData, 'id', 'Missing required parameter: id');
        Assert::uuid($requestData['id'], 'Parameter id must be a valid UUID');
        Assert::uuid($userUuid, 'Authenticated user uuid must be a valid UUID');

        return new self((string) $requestData['id'], $userUuid);
    }
}
