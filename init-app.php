<?php

spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});

use Helpers\Settings;

printf("Local database username: %s\n", Settings::env('DATABASE_USER'));
printf("Local database password (hashed): %s\n", password_hash(Settings::env('DATABASE_USER_PASSWORD'), PASSWORD_DEFAULT));