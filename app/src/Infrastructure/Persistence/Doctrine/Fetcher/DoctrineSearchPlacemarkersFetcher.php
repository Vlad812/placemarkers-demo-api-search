<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersFetcherInterface;
use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersQuery;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class DoctrineSearchPlacemarkersFetcher implements SearchPlacemarkersFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(SearchPlacemarkersQuery $query): array
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
              AND ST_DWithin(
                p.geom,
                ST_SetSRID(ST_MakePoint(:lon, :lat), 4326)::geography,
                :radius
            )
        SQL;

        $params = [
            'user_uuid' => $query->userUuid,
            'lon' => $query->area->center()->longitude(),
            'lat' => $query->area->center()->latitude(),
            'radius' => $query->area->radiusMeters(),
        ];
        $types = [];

        if ($query->types !== []) {
            $sql .= ' AND p.type_id IN (:types)';
            $params['types'] = $query->types;
            $types['types'] = ArrayParameterType::STRING;
        }

        if ($query->tags !== []) {
            $sql .= ' AND (
                SELECT COUNT(DISTINCT pt_filter.tag_id)
                FROM placemarker_tags pt_filter
                WHERE pt_filter.placemarker_id = p.id
                  AND pt_filter.tag_id IN (:tags)
            ) = :tags_count';
            $params['tags'] = $query->tags;
            $params['tags_count'] = count($query->tags);
            $types['tags'] = ArrayParameterType::STRING;
        }

        $sql .= ' GROUP BY p.id ORDER BY p.created_at DESC';

        $result = $this->connection->executeQuery($sql, $params, $types)->fetchAllAssociative();

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
