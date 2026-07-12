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
                COALESCE(json_agg(pt.tag_id) FILTER (WHERE pt.tag_id IS NOT NULL), '[]') as tags
            FROM placemarkers p
            LEFT JOIN placemarker_tags pt ON p.id = pt.placemarker_id
            WHERE p.id = :id
              AND p.user_uuid = :user_uuid
            GROUP BY p.id
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
            'tags' => json_decode($row['tags'], true),
            'description' => $row['description'],
            'created_at' => (string) $row['created_at'],
        ];
    }
}
