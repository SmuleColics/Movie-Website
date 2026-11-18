<?php
// Copy this file to config.php (same folder) and fill in your own values.
// config.php is ignored by git, so passwords and keys never reach GitHub.
return [
    'debug' => false,          // true shows PHP errors on screen (local testing only)

    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'db_finalproject',

    // Gmail used to send reset codes and approval emails.
    // Make an App Password at https://myaccount.google.com/apppasswords
    'mail_username'  => '',
    'mail_password'  => '',
    'mail_from_name' => 'CineVault',
];
