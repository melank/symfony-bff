<?php

namespace App\Controller;

use App\Service\GraphQLClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class BffController extends AbstractController
{
    public function __construct(
        private readonly GraphQLClient $graphQLClient,
    ) {
    }

    #[Route('/graphql', name: 'bff_graphql', methods: ['POST'])]
    public function graphql(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $query = $data['query'] ?? '';
        $variables = $data['variables'] ?? [];

        try {
            $result = $this->graphQLClient->query($query, $variables);
            return $this->json($result);
        } catch (\Throwable $e) {
            return $this->json([
                'errors' => [['message' => $e->getMessage()]],
            ], 500);
        }
    }
}
