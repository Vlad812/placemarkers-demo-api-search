<?php

declare(strict_types=1);

namespace App\Application\Query\GetUserTags;

interface GetUserTagsFetcherInterface
{
    /**
     * @return list<array{id: string, type_id: string, name: string, description: ?string}>
     */
    public function fetch(GetUserTagsQuery $query): array;
}
