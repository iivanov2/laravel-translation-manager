<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
    'route'          => [
        'prefix'     => 'translations',
        'middleware' => 'auth',
    ],
    
    /**
     * If enabled=true notify admin when translations are published (because git commit and push could be needed).
     * You need to implement your own global function simple_email(['to'=>'john@smith.com','body'=>'...']) to use this options, otherwise set it false
     */
    'notify_on_publish' => [
        'enabled' => false,
        'emails' => ['admin1@example.com','admin2@example.com'],
        'email_text' => 'New translations were published. Git commit and push needed.'
    ],
    
    /**
     * Exclude specific groups from Laravel Translation Manager.
     * This is useful if, for example, you want to avoid editing the official Laravel language files.
     *
     * @type array
     *
     *    array(
     *        'pagination',
     *        'reminders',
     *        'validation',
     *    )
     */
    'exclude_groups' => [],

    /**
     * Exclude specific languages from Laravel Translation Manager.
     */
    'exclude_langs'  => [], //this option is deprecated, it is using \storage\.ignore_locales scheme: ["en","zb"]

    /**
     * Export translations with keys output alphabetically.
     */
    'sort_keys '     => false,

    'trans_functions' => [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
        '$trans.get',
    ],

];
