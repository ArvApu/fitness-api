<?php

if (!function_exists('take'))
{
    /**
     * Take element out from array or object by key or property name
     *
     * Removes element from array/object and returns it
     * @param string $by
     * @param object|array $from
     * @return mixed
     */
    function take(&$from, string $by)
    {
        if (is_array($from)) {
            $temp = $from[$by] ?? null;
            unset($from[$by]);
            return $temp;
        }

        if (is_object($from)) {
            $temp = $from->{$by} ?? null;
            unset($from->{$by});
            return $temp;
        }

        return null;
    }
}


if (!function_exists('ui_url'))
{
    /**
     * Generate a url for the application's ui.
     *
     * @param string|null $path
     * @param array $query
     * @param string|null $schema
     * @param int|null $port
     * @return string
     */
    function ui_url(string $path = null, array $query = [], string $schema = null, int $port = null): string
    {
        $url = env('APP_UI_URL', 'http://localhost');
        if (null !== $port) {
            $url .= ':' . $port;
        }
        if (null !== $path) {
            $url .= '/' . ltrim($path, '/');
        }
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        return (null === $schema ? $url : ($schema . '://' . $url));
    }
}

if (!function_exists('get_yt_embed_url'))
{
    /**
     * Generate a embed youtube url.
     *
     * @param string $url
     * @return string
     */
    function get_yt_embed_url(string $url): string
    {
        $urlParts = explode('/', $url);
        $vidId = explode('&', str_replace('watch?v=', '', end($urlParts)));

        return 'https://www.youtube.com/embed/' . $vidId[0];
    }
}
