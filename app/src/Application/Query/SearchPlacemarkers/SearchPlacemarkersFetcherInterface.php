<?php

declare(strict_types=1);

namespace App\Application\Query\SearchPlacemarkers;

interface SearchPlacemarkersFetcherInterface
{
    /**
     * @return list<array{
     *     id: string,
     *     name: string,
     *     lat: float,
     *     lon: float,
     *     description: string|null
     * }>
     */
    public function fetch(SearchPlacemarkersQuery $query): array;
}
