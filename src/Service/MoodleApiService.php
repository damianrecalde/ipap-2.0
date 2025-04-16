<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoodleApiService
{
    private string $moodleUrl;
    private string $moodleToken;
    private HttpClientInterface $httpClient;

    public function __construct(string $moodleUrl, string $moodleToken, HttpClientInterface $httpClient)
    {
        $this->moodleUrl = rtrim($moodleUrl, '/');
        $this->moodleToken = $moodleToken;
        $this->httpClient = $httpClient;
    }

    public function call(string $function, array $params = []): array
    {
        $url = "{$this->moodleUrl}/webservice/rest/server.php";

        $response = $this->httpClient->request('POST', $url, [
            'body' => array_merge($params, [
                'wstoken' => $this->moodleToken,
                'moodlewsrestformat' => 'json',
                'wsfunction' => $function,
            ]),
        ]);

        return $response->toArray(false);
    }
}
