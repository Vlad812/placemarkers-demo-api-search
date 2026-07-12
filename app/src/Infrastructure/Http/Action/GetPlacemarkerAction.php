<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action;

use App\Application\Query\GetPlacemarker\GetPlacemarkerHandler;
use App\Application\Query\GetPlacemarker\GetPlacemarkerQuery;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/search/placemarkers/{id}',
    name: 'placemarker_get',
    methods: ['GET'],
)]
final class GetPlacemarkerAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly GetPlacemarkerHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $query = GetPlacemarkerQuery::createFromRawValues(
            ['id' => (string) $request->attributes->get('id')],
            $this->userProvider->getUserUuid(),
        );

        $result = ($this->handler)($query);

        return $this->respondJson($result);
    }
}
