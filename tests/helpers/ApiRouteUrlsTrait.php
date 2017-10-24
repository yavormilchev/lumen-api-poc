<?php

namespace App\Tests\Helpers;

trait ApiRouteUrlsTrait
{
    function getApiRouteUrl(string $route_name, array $params = [])
    {
        $invalid_url = route($route_name, $params);
        $url_parts = explode(':', $invalid_url);
        $path = array_pop($url_parts);
        $base_url = env('APP_URL', 'http://localhost');

        return rtrim($base_url, '/') . '/' . ltrim($path, '/');
    }
}