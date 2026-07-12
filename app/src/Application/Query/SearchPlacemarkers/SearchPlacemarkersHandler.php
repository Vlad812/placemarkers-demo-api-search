<?php

declare(strict_types=1);

namespace App\Application\Query\SearchPlacemarkers;

final readonly class SearchPlacemarkersHandler
{
    public function __construct(
        private SearchPlacemarkersFetcherInterface $fetcher,
    ) {
    }

    /**
     * @return list<array{
     *     id: string,
     *     name: string,
     *     lat: float,
     *     lon: float,
     *     description: string|null
     * }>
     */
    public function __invoke(SearchPlacemarkersQuery $query): array
    {
        return $this->fetcher->fetch($query);
    }
}
