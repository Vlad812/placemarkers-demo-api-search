<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesHandler;
use App\Application\Query\GetPlacemarkerTypes\GetPlacemarkerTypesQuery;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/search/types',
    name: 'types_list',
    methods: ['GET'],
)]
final class GetTypesAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly GetPlacemarkerTypesHandler $handler,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $query = GetPlacemarkerTypesQuery::createFromRawValues($request->query->all());
        $results = ($this->handler)($query);

        return $this->respondJson($results);
    }
}
