<?php

/**
 * If will be available class with name \App\Bootstrap and it will be extends from \Light\BootstrapAbstract
 * it will be initialized and called each function
 */

return [

    'light' => [

        // Namespace for the modules
        // If not exists will be used app folder path - loader.namespace \\Controller, \\View and etc.
        'modules'   => '\\App\\Module',

        // Display exceptions
        // Not required - just will be used as false
        'exception' => true,

        // Cookie
        'cookie' => [
            'namespace' => null,
            'domain' => 'domain.com'
        ],

        // PHP ini vars
        'phpIni' => [
            'display_startup_errors' => '1',
            'display_errors' => '1',
        ],

        // Startup functions
        'startup' => [
            'error_reporting' => E_ALL,
            'set_time_limit' => 30
        ],

        'loader' => [

            // Path to app directory
            'path' => realpath(dirname(__FILE__)) . '/app',

            // Application namespace
            'namespace' => 'App',
        ],

        'asset' => [

            // Adding ?_=microtime()
            'underscore' => true,

            // prefix for assets
            'prefix' => '/assets'
        ]
    ],


    'router' => [

        // For all domains and subdomains
        '*' => [

            // Module name for any domain
            // will be ignored if light.modules will not be specified
            'module' => 'face',

            // If TRUE disallow to use unspecified urls
            // Ex. /{controller}/{action}/param1/value1
            'strict' => false,

            'routes' => [

                // Route URL
                '/uri/:param1/:param2' => [

                    // Controller
                    'controller' => 'index',

                    // Action
                    'action' => 'item',

                    // Injector
                    //
                    // Index::item($param1, $param2);
                    //
                    // If injector function for the argument will be specified,
                    // it will be initialized with returned value.
                    //
                    // If injector function for the argument will NOT be specified,
                    // it will be initialized just with string values parsed from request URI
                    //
                    // Ex. Index::item(SomeClass $param1, int $param2);
                    // $param1 - will be initialized - new SomeClass( {string value from requested URI} )
                    // $param2 - will try URI parameter convert to to INT
                    //
                    'injector' => [

                        'param1' => function ($param1) {

                            // ....

                            return $param1;
                        },
                    ]
                ],
            ],
        ],

        'domain.com' => [
            // Possible settings explained for '*'
        ],

        // Exactly for CLI mode
        'cli' => [
            // Possible settings explained for '*'
        ],
    ]
];