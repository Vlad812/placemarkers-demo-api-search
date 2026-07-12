<?php

declare(strict_types=1);

namespace App\Application\Query\GetPlacemarkerTypes;

final readonly class GetPlacemarkerTypesHandler
{
    public function __construct(
        private GetPlacemarkerTypesFetcherInterface $fetcher,
    ) {
    }

    /**
     * @return list<array{slug: string, name: string}>
     */
    public function __invoke(GetPlacemarkerTypesQuery $query): array
    {
        return $this->fetcher->fetch($query);
    }
}
