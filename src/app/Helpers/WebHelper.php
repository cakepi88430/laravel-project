<?php
if ( ! function_exists('site_url'))
{
    function site_url()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
                    ? "https://"
                    : "http://";
        $domainName = $_SERVER['HTTP_HOST'];

        return $protocol.$domainName;
    }
}

if ( ! function_exists('url_path'))
{
    function url_path($path)
    {
        $startPos = strpos($path, config('custom.folder_separator'));
        $url = substr($path, $startPos + 1);
        return str_replace(config('custom.folder_separator'), '/', $url);
    }
}

if ( ! function_exists('check_access'))
{
    function check_access($path)
    {
        $headers = get_headers($path);
        return substr($headers[0], 9, 3);
    }
}

if ( ! function_exists('is_ipv6') )
{
    function is_ipv6($ip)
    {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        } else {
            return false;
        }
    }
}

if ( ! function_exists('is_ipv4') )
{
    function is_ipv4($ip)
    {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        } else {
            return false;
        }
    }
}

if ( ! function_exists('is_valid_v4') )
{
    function is_valid_v4($ip)
    {
       return sprintf("%u", ip2long($ip));
    }
}

if ( ! function_exists('url_available') )
{
    function url_available($url)
    {
        $client = new GuzzleHttp\Client();
        try {
            $client->head($url);
            return true;
        } catch (GuzzleHttp\Exception\ClientException $e) {
            return false;
        }
    }
}

if ( ! function_exists('auth_user'))
{
    function auth_user()
    {
        $user = auth('sanctum')->user();
        if(!$user) {
            $user = null;
        }
        return $user;
    }
}
