<?php

return [
    // versione corrente
    'latest' => env('APP_LATEST_VERSION', '0.7.0'),

    // base url pubblico ai file (di solito APP_URL/storage/updates)
    'base_url' => env('APP_UPDATE_BASE_URL', env('APP_URL').'/storage/updates'),

    // pattern dei file per piattaforma (usa :version come placeholder)
    'platforms' => [
        'windows' => [
            // NSIS setup (.exe). L’updater v2 va bene anche con .exe + .sig
            'pattern' => 'gestionale-a-villetta_:version_x64-setup.exe',
        ],
        'linux' => [
            'pattern' => 'gestionale-a-villetta_:version_amd64.AppImage',
        ],
        'darwin' => [
            'pattern' => 'gestionale-a-villetta_:version_x64.dmg',
        ],
    ],

    'notes' => env('APP_UPDATE_NOTES', ''),
];
