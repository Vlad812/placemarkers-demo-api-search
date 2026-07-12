<?php

declare(strict_types=1);

namespace App\Application\Query\GetPlacemarker;

use App\Domain\Exception\PlacemarkerNotFoundException;

final readonly class GetPlacemarkerHandler
{
    public function __construct(
        private GetPlacemarkerFetcherInterface $fetcher,
    ) {
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     lat: float,
     *     lon: float,
     *     type_id: string,
     *     tags: list<string>,
     *     description: string|null,
     *     created_at: string
     * }
     */
    public function __invoke(GetPlacemarkerQuery $query): array
    {
        $result = $this->fetcher->fetch($query);

        if ($result === null) {
            throw PlacemarkerNotFoundException::withId($query->id);
        }

        return $result;
    }
}
