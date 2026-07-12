<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fetcher;

use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesFetcherInterface;
use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class DoctrineGetPlacemarkerTypesFetcher implements GetPlacemarkerTypesFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function fetch(GetPlacemarkerTypesQuery $query): array
    {
        $sql = <<<SQL
            SELECT
                slug,
                name
            FROM placemarker_types
            ORDER BY name ASC
        SQL;

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }
}
