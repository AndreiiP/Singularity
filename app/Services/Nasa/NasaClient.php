<?php

namespace App\Services\Nasa;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class NasaClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $timeout = 10,
        private readonly int $cacheTtl = 5
    ) {}

    /**
     * https://api.nasa.gov/planetary/apod?api_key=...&date=YYYY-MM-DD
     */
    public function apod(?string $date = null, bool $hd = false): array
    {
        $params = array_filter([
            'date'   => $date,
            'hd'     => $hd ? 'true' : null,
        ]);

        return $this->get('/planetary/apod', $params, $this->cacheTtl);
    }

    /**
     * https://api.nasa.gov/mars-photos/api/v1/rovers/{rover}/photos?earth_date=YYYY-MM-DD
     */
    public function marsRoverPhotos(string $rover, string $earthDate, ?string $camera = null, int $page = 1): array
    {
        $params = array_filter([
            'earth_date' => $earthDate,
            'camera'     => $camera,
            'page'       => $page,
        ]);

        return $this->get("/mars-photos/api/v1/rovers/{$rover}/photos", $params, $this->cacheTtl);
    }

    /**
     * https://images-api.nasa.gov/search?q=...
     */
    public function searchImages(string $query, ?string $mediaType = 'image', int $page = 1): array
    {
        $url = 'https://images-api.nasa.gov/search';
        $params = array_filter([
            'q'          => $query,
            'media_type' => $mediaType,
            'page'       => $page,
        ]);

        return $this->rawGet($url, $params, $this->cacheTtl);
    }

    private function get(string $path, array $params = [], ?int $cacheMinutes = null): array
    {
        $url = $this->baseUrl . $path;

        $params = array_merge($params, ['api_key' => $this->apiKey]);

        $cacheKey = $this->cacheKey('GET', $url, $params);
        if ($cacheMinutes) {
            return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($url, $params) {
                return $this->request($url, $params);
            });
        }

        return $this->request($url, $params);
    }

    private function rawGet(string $url, array $params = [], ?int $cacheMinutes = null): array
    {
        $cacheKey = $this->cacheKey('GET', $url, $params);
        if ($cacheMinutes) {
            return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($url, $params) {
                return $this->request($url, $params, false);
            });
        }

        return $this->request($url, $params, false);
    }

    private function request(string $url, array $params = [], bool $withKeyHeaders = true): array
    {
        try {
            $resp = Http::retry(3, 200)
                ->timeout($this->timeout)
                ->acceptJson()
                ->get($url, $params);

            $resp->throw();

            return $resp->json() ?? [];
        } catch (ConnectionException $e) {
            throw new \RuntimeException('NASA API connection failed', previous: $e);
        } catch (RequestException $e) {
            // HTTP 4xx/5xx
            $status = optional($e->response)->status();
            $body   = optional($e->response)->json() ?: optional($e->response)->body();
            throw new \RuntimeException("NASA API error ({$status}): " . json_encode($body), previous: $e);
        }
    }

    private function cacheKey(string $method, string $url, array $params): string
    {
        return 'nasa:' . md5($method . '|' . $url . '|' . json_encode($params));
    }
}
