<?php

use Illuminate\Http\Request;

return [
    //'proxies' => '*',
    'proxies' => array_filter(
        array_map(
            'trim',
            explode(',', env('TRUSTED_PROXIES', ''))
        )
    ),
    'headers' =>
    Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO,
];

// return [
//     'proxies' => null, // или []
//     'headers' => 0,
// ];
