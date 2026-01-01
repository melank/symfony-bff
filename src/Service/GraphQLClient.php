<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GraphQLClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $graphqlEndpoint,
    ) {
    }

    public function query(string $query, array $variables = []): array
    {
        $response = $this->httpClient->request('POST', $this->graphqlEndpoint, [
            'json' => [
                'query' => $query,
                'variables' => $variables,
            ],
        ]);

        return $response->toArray();
    }

    public function mutation(string $mutation, array $variables = []): array
    {
        return $this->query($mutation, $variables);
    }
}
