<?php
// Copy this file to config.php (same folder) and fill in your own values.
// config.php is ignored by git, so passwords and keys never reach GitHub.
return [
    'debug' => false,          // true shows PHP errors on screen (local testing only)
    'timezone' => 'Asia/Manila', // PHP and MySQL both use this

    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'db_finalproject',

    // Gmail used to send reset codes and approval emails.
    // Make an App Password at https://myaccount.google.com/apppasswords
    'mail_username'  => '',
    'mail_password'  => '',
    'mail_from_name' => 'CineVault',

    // TMDB (The Movie Database) for the admin "Search TMDB" import.
    // Free key: create a TMDB account, then Settings > API. Paste either one:
    'tmdb_api_key'    => '', // the "API Key" (v3)
    'tmdb_read_token' => '', // or the "API Read Access Token" (v4)
    'tmdb_language'   => 'en-US',
    'tmdb_region'     => 'PH', // used for the age rating; falls back to US
];
