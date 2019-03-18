<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => public_path().'/storage',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'UpLoadFile' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => 'storage',
            'visibility' => 'public',
        ],

        'FclAccount' => [
            'driver' => 'local',
            'root' => storage_path('app/public/AccountFcl'),
            'url' => 'storage/app/public/AccountFcl',
            'visibility' => 'public',
        ],

        'FclImport' => [
            'driver' => 'local',
            'root' => storage_path('app/public/ImportFcl'),
            'url' => 'storage/app/public/ImportFcl',
            'visibility' => 'public',
        ],

        'FclRequest' => [
            'driver' => 'local',
            'root' => storage_path('app/public/RequestFcl'),
            'url' => 'storage/app/public/RequestFcl',
            'visibility' => 'public',
        ],

        'DownLoadFile' => [
            'driver' => 'local',
            'root' => storage_path('app/files'),
            'url' => 'storage',
            'visibility' => 'public',
        ],

        'RequestFiles' => [
            'driver' => 'local',
            'root' => storage_path('app/exports'),
            'url' => 'storage',
            'visibility' => 'public',
        ],

        'image' => [
            'driver' => 'local',
            'root' => public_path(),
            'url' => 'public',
            'visibility' => 'public',
        ],

        'logos' => [
            'driver' => 'local',
            'root' => storage_path('app/logos'),
            'url' => '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' 	  => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),

        ],
        's3_upload' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_UPLOAD'),

        ],     
    ],

];
