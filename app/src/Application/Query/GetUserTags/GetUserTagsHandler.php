<?php

declare(strict_types=1);

namespace App\Application\Query\GetUserTags;

final readonly class GetUserTagsHandler
{
    public function __construct(
        private GetUserTagsFetcherInterface $fetcher,
    ) {
    }

    /**
     * @return list<array{id: string, name: string, description: ?string}>
     */
    public function __invoke(GetUserTagsQuery $query): array
    {
        return $this->fetcher->fetch($query);
    }
}
