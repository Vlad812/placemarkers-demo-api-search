<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Query\GetUserTags\GetUserTagsHandler;
use App\Application\Query\GetUserTags\GetUserTagsQuery;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/search/tags',
    name: 'tags_list',
    methods: ['GET'],
)]
final class GetTagsAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly GetUserTagsHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $query = GetUserTagsQuery::createFromRawValues(
            $request->query->all(),
            $this->userProvider->getUserUuid(),
        );
        $results = ($this->handler)($query);

        return $this->respondJson($results);
    }
}
