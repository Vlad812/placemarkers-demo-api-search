<?php

declare(strict_types=1);

namespace App\Application\Query\GetRecentPlacemarkers;

final readonly class GetRecentPlacemarkersHandler
{
    public function __construct(
        private GetRecentPlacemarkersFetcherInterface $fetcher,
    ) {
    }

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
    public function __invoke(GetRecentPlacemarkersQuery $query): array
    {
        return $this->fetcher->fetch($query);
    }
}
