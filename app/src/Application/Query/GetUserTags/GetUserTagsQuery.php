<?php

declare(strict_types=1);

namespace App\Application\Query\GetUserTags;

use Webmozart\Assert\Assert;

final readonly class GetUserTagsQuery
{
    public function __construct(
        public string $userUuid,
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        Assert::uuid($userUuid, 'Authenticated user uuid must be a valid UUID');

        return new self($userUuid);
    }
}
