<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PandascoreService {
    
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function getLeagueSeries() {
        $response = $this->client->request('GET', 'https://api.pandascore.co/lol/series', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer HQ_3StbmoOUVZ6WWcaxBxsaHWU-rCboLvWA8XE-giGmZ-RqxZKU',
            ]
        ]);

        return $response->toArray();
    }

    public function getLeagueTournaments() {
        $response = $this->client->request('GET', 'https://api.pandascore.co/lol/tournaments', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer HQ_3StbmoOUVZ6WWcaxBxsaHWU-rCboLvWA8XE-giGmZ-RqxZKU',
            ]
        ]);

        return $response->toArray();
    }
}