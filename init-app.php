<?php

spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $file = __DIR__ . '/'  . str_replace('\\', '/', $class). '.php';
    if (file_exists(stream_resolve_include_path($file))) include($file);
});

use Helpers\Settings;
use Database\MySQLWrapper;
/*
 * https://www.php.net/manual/en/class.mysqli.php で利用可能なすべてのメソッドを確認できます。
 */
$mysqli = new MySQLWrapper('localhost', Settings::env('DATABASE_USER'), Settings::env('DATABASE_USER_PASSWORD'), Settings::env('DATABASE_NAME'));

// https://www.php.net/manual/en/mysqli.get-charset.php
$charset = $mysqli->get_charset();

if($charset === null) throw new Exception('Charset could be read');

// データベースの文字セット、照合順序、統計情報について取得します。
printf(
    "%s's charset: %s.%s",
    Settings::env('DATABASE_NAME'),
    $charset->charset,
    PHP_EOL
);

printf(
    "collation: %s.%s",
    $charset->collation,
    PHP_EOL
);

// 接続を閉じるには、closeメソッドを使用します。
$mysqli->close();