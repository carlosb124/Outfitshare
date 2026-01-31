<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmartCropService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey = 'TODO_API_KEY'
    ) {
    }

    public function removeBackground(string $imageUrl): ?string
    {
        // Placeholder implementation for 'remove.bg' or similar API
        // In production, you would make an actual HTTP request here.

        // Example logic:
        // $response = $this->client->request('POST', 'https://api.remove.bg/v1.0/removebg', [
        //     'headers' => ['X-Api-Key' => $this->apiKey],
        //     'body' => ['image_url' => $imageUrl]
        // ]);

        // return $response->toArray()['url'] ?? null;

        return $imageUrl; // Return original if not implemented
    }
}
