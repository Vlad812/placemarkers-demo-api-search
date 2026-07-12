<?php

declare(strict_types=1);

namespace App\Application\Query\GetPlacemarkerTypes;

interface GetPlacemarkerTypesFetcherInterface
{
    /**
     * @return list<array{slug: string, name: string}>
     */
    public function fetch(GetPlacemarkerTypesQuery $query): array;
}
