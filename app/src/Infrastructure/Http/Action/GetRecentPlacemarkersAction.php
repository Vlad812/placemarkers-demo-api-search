<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersHandler;
use App\Application\Query\GetRecentPlacemarkers\GetRecentPlacemarkersQuery;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/search/recent',
    name: 'placemarkers_recent',
    methods: ['GET'],
)]
final class GetRecentPlacemarkersAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly GetRecentPlacemarkersHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $query = GetRecentPlacemarkersQuery::createFromRawValues(
            $request->query->all(),
            $this->userProvider->getUserUuid(),
        );

        $results = ($this->handler)($query);

        return $this->respondJson($results);
    }
}
