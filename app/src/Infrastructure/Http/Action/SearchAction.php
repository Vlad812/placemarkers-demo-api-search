<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersHandler;
use App\Application\Query\SearchPlacemarkers\SearchPlacemarkersQuery;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/search',
    name: 'search',
    methods: ['GET'],
)]
final class SearchAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly SearchPlacemarkersHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $query = SearchPlacemarkersQuery::createFromRawValues(
            $request->query->all(),
            $this->userProvider->getUserUuid(),
        );

        $results = ($this->handler)($query);

        return $this->respondJson($results);
    }
}
