<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\GetUserTags\GetUserTagsFetcherInterface;
use App\Application\Query\GetUserTags\GetUserTagsQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class DoctrineGetUserTagsFetcher implements GetUserTagsFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(GetUserTagsQuery $query): array
    {
        $sql = <<<SQL
            SELECT
                id,
                name,
                description
            FROM tags
            WHERE user_uuid = :user_uuid
            ORDER BY created_at DESC
        SQL;

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('user_uuid', $query->userUuid);

        return $stmt->executeQuery()->fetchAllAssociative();
    }
}
