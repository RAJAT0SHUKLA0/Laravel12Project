<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;

class FcmService
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');

        $this->client = new Client();
        $this->client->setAuthConfig(config('firebase.credentials'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    private function getAccessToken()
    {
        $token = $this->client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    public function send(array $message)
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $response = Http::withToken($this->getAccessToken())
            ->post($url, ['message' => $message]);
        return $response->json();
    }
}
