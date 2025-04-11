<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CourseService
{
    private $client;
    private $moodleToken;
    private $moodleUrl;

    public function __construct(HttpClientInterface $client, string $moodleToken, string $moodleUrl)
    {
        $this->client = $client;
        $this->moodleToken = $moodleToken;
        $this->moodleUrl = $moodleUrl;
    }

    public function listCourses(): array
    {
        $response = $this->client->request('GET', $this->moodleUrl, [
            'query' => [
                'wstoken' => $this->moodleToken,
                'wsfunction' => 'core_course_get_courses',
                'moodlewsrestformat' => 'json',
            ],
        ]);

        $data = $response->toArray();
        return $data;
    }
}