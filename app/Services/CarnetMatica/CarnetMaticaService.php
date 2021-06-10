<?php

namespace App\Services\CarnetMatica;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class CarnetMaticaService
{
    private $url;

    public function __construct()
    {
        $this->url = config('services.carnet_matica.url');
    }

    public function getBearerToken(): ?string
    {
        $queryParams = [
            'query' => [
                'username' => 'marin.bosnjak3@skole.hr',
                'password' => '3cmw6drK',
                'token' => '319DC066EC9A3FC55B09DAE96941EF11'
            ]
        ];

        $response = Http::withOptions($queryParams)->post($this->url . '/api/Authorize/Authenticate', $queryParams);

        if ($response->successful())
            return $response->body();

        return null;
    }

    public function getSchool(string $sifra, string $podsifra): ?array
    {
        $response = Http::withToken($this->getBearerToken())
            ->get($this->url . '/GetUstanova', [
                'Sifra' => $sifra,
                'Podsifra' => $podsifra
            ]);

        return $response->successful() ? $response->json() : null;
    }

    public function getDepartments(string $sifra, string $podsifra): ?array
    {
        $response = Http::withToken($this->getBearerToken())
            ->get($this->url . '/Odjeljenja', [
                'Sifra' => $sifra,
                'Podsifra' => $podsifra,
                '......TODO......'
            ]);

        return $response->successful() ? $response->json() : null;
    }

    public function getStudentByOib(string $oib): ?array
    {
        $response = Http::withToken($this->getBearerToken())
            ->get($this->url . '/api/Ucenik/' . $oib);

        return $response->successful() ? $response->json() : null;
    }
}
