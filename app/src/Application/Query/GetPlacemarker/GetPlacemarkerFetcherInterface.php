<?php

declare(strict_types=1);

namespace App\Application\Query\GetPlacemarker;

interface GetPlacemarkerFetcherInterface
{
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
     * }|null
     */
    public function fetch(GetPlacemarkerQuery $query): ?array;
}
