<?php

declare(strict_types=1);

namespace App\Application\Query\GetRecentPlacemarkers;

interface GetRecentPlacemarkersFetcherInterface
{
    /**
     * @return list<array{
     *     id: string,
     *     name: string,
     *     lat: float,
     *     lon: float,
     *     type_id: string,
     *     tags: list<string>,
     *     description: string|null
     * }>
     */
    public function fetch(GetRecentPlacemarkersQuery $query): array;
}
