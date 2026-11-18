<?php
// Settings come from includes/config.php (not in git). See config.example.php.
if (!function_exists('cv_config')) {
    function cv_config($key, $default = null)
    {
        static $config = null;
        if ($config === null) {
            $file = __DIR__ . '/config.php';
            $config = is_file($file) ? require $file : require __DIR__ . '/config.example.php';
        }
        return $config[$key] ?? $default;
    }
}

try {
    $con = mysqli_connect(cv_config('db_host'), cv_config('db_user'), cv_config('db_pass'), cv_config('db_name'));
} catch (mysqli_sql_exception $ex) {
    $con = false;
}

if (!$con) {
    http_response_code(500);
    die(cv_config('debug') ? 'Connection Error: ' . mysqli_connect_error() : 'The site cannot reach its database right now.');
}
mysqli_set_charset($con, 'utf8mb4');

require_once __DIR__ . '/bootstrap.php';
