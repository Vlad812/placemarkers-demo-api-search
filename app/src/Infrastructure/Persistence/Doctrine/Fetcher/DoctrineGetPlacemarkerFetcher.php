<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\GetPlacemarker\GetPlacemarkerFetcherInterface;
use App\Application\Query\GetPlacemarker\GetPlacemarkerQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class DoctrineGetPlacemarkerFetcher implements GetPlacemarkerFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(GetPlacemarkerQuery $query): ?array
    {
        $sql = <<<SQL
            SELECT
                p.id,
                p.name,
                p.lat,
                p.lon,
                p.type_id,
                p.description,
                p.created_at,
                COALESCE(p.tags_jsonb, '[]'::jsonb) as tags
            FROM placemarkers p
            WHERE p.id = :id
              AND p.user_uuid = :user_uuid
        SQL;

        $row = $this->connection->executeQuery($sql, [
            'id' => $query->id,
            'user_uuid' => $query->userUuid,
        ])->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return [
            'id' => $row['id'],
            'name' => $row['name'],
            'lat' => (float) $row['lat'],
            'lon' => (float) $row['lon'],
            'type_id' => $row['type_id'] ?? 'default',
            'tags' => TagsDecoder::decode($row['tags'] ?? null),
            'description' => $row['description'],
            'created_at' => (string) $row['created_at'],
        ];
    }
}
