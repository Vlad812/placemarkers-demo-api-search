<?php

declare(strict_types=1);

namespace App\Application\Query\GetPlacemarkerTypes;

final readonly class GetPlacemarkerTypesQuery
{
    /**
     * @param array<string, mixed> $requestData
     */
    public static function createFromRawValues(array $requestData): self
    {
        return new self();
    }
}
