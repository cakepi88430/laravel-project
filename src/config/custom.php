<?php

return array(
	'folder_separator' => env('FOLDERSEPARATOR', "\\"),
	'line_break'=> env('LINEBREAK', "\r\n"),
	'system_encode'=>env('SYSTEMENCODE', 'BIG5'),
    'internal_ips'=>(env('INTERNALIPS') ? array_filter(explode(';', env('INTERNALIPS'))) : []),
    'api_whitelist_ips'=>(env('APIWHITELISTIPS') ? array_filter(explode(';', env('APIWHITELISTIPS'))) : []),
    'base64'=>env('BASE64', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_'),
    'app_localhost_name' => env('APP_LOCALHOST_NAME', ''),
);