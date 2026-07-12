<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersFetcherInterface;
use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class DoctrineGetRecentPlacemarkersFetcher implements GetRecentPlacemarkersFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(GetRecentPlacemarkersQuery $query): array
    {
        $sql = <<<SQL
            SELECT
                p.id,
                p.name,
                p.lat,
                p.lon,
                p.type_id,
                p.description,
                COALESCE(json_agg(pt.tag_id) FILTER (WHERE pt.tag_id IS NOT NULL), '[]') as tags
            FROM placemarkers p
            LEFT JOIN placemarker_tags pt ON p.id = pt.placemarker_id
            WHERE p.user_uuid = :user_uuid
            GROUP BY p.id
            ORDER BY p.created_at DESC
        SQL;

        $params = ['user_uuid' => $query->userUuid];

        if ($query->limit > 0) {
            $sql .= ' LIMIT :limit';
            $params['limit'] = $query->limit;
        }

        $result = $this->connection->executeQuery($sql, $params)->fetchAllAssociative();

        return array_map(static fn (array $row): array => [
            'id' => $row['id'],
            'name' => $row['name'],
            'lat' => (float) $row['lat'],
            'lon' => (float) $row['lon'],
            'type_id' => $row['type_id'] ?? 'default',
            'tags' => json_decode($row['tags'], true),
            'description' => $row['description'],
        ], $result);
    }
}
