<?php

use Illuminate\Http\Request;

return [
    'proxies' => '*',
    'headers' => [
        Request::HEADER_FORWARDED => null,
        Request::HEADER_X_FORWARDED_FOR => 'X-FORWARDED-FOR',
        Request::HEADER_X_FORWARDED_HOST => 'X-FORWARDED-HOST',
        Request::HEADER_X_FORWARDED_PORT => 'X-FORWARDED-PORT',
        Request::HEADER_X_FORWARDED_PROTO => 'X-FORWARDED-PROTO',
    ],
];
