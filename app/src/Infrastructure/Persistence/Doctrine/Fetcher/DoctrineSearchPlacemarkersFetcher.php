<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersFetcherInterface;
use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use JsonException;

final readonly class DoctrineSearchPlacemarkersFetcher implements SearchPlacemarkersFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     * @throws JsonException
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
                COALESCE(p.tags_jsonb, '[]'::jsonb) as tags
            FROM placemarkers p
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

        if ($query->filters !== []) {
            $orParts = [];
            foreach ($query->filters as $index => $filter) {
                $typeParam = 'filter_type_' . $index;
                $condition = "p.type_id = :{$typeParam}";
                $params[$typeParam] = $filter['type_id'];

                if ($filter['tags'] !== []) {
                    $tagsParam = 'filter_tags_' . $index;
                    $condition .= " AND p.tags_jsonb @> :{$tagsParam}::jsonb";
                    $params[$tagsParam] = json_encode(array_values($filter['tags']), JSON_THROW_ON_ERROR);
                }

                $orParts[] = "({$condition})";
            }

            $sql .= ' AND (' . implode(' OR ', $orParts) . ')';
        }

        $sql .= ' ORDER BY p.created_at DESC';

        $result = $this->connection->executeQuery($sql, $params)->fetchAllAssociative();

        return array_map(static fn (array $row): array => [
            'id' => $row['id'],
            'name' => $row['name'],
            'lat' => (float) $row['lat'],
            'lon' => (float) $row['lon'],
            'type_id' => $row['type_id'] ?? 'default',
            'tags' => TagsDecoder::decode($row['tags'] ?? null),
            'description' => $row['description'],
        ], $result);
    }
}
