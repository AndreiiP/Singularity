<?php

namespace App\Http\Controllers;

use App\Services\Nasa\NasaClient;
use Illuminate\Http\JsonResponse;

class SpaceController
{
    public function __construct(private readonly NasaClient $nasa) {}

    public function apod(): JsonResponse
    {
        $data = $this->nasa->apod();
        return response()->json($data);
    }

    public function mars(string $rover, string $date): JsonResponse
    {
        $data = $this->nasa->marsRoverPhotos($rover, $date);
        return response()->json($data);
    }

    public function search(string $q): JsonResponse
    {
        return response()->json($this->nasa->searchImages($q));
    }
}
