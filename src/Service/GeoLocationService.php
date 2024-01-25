<?php
// src/Service/GeoLocationService.php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class GeoLocationService
{
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    public function getCityNameByCoordinates(float $latitude, float $longitude): ?string
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude&zoom=18&addressdetails=1";

        try {
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();

            return $data['address']['city'] ?? null;
        } catch (\Exception $e) {
            // Gérer les erreurs (par exemple, journaliser l'erreur)
            return null;
        }
    }
}
