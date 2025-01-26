<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InpostApiClient
{
    public function __construct(
        private readonly string $inpostBaseUrl,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function fetchData(string $resource, ?string $city): string
    {
        try {
            $url = $this->inpostBaseUrl . '/' . $resource;
            $url .= $city ? '?city=' . $city : '';
            $response = $this->httpClient->request('GET', $url);

            return $response->getContent();
        } catch (ClientExceptionInterface $exception) {
            throw $exception;
        }
    }
}
