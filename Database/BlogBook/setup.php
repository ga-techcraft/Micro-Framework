<?php

use Database\MySQLWrapper;

// SQLファイルを実行する関数
function runSqlQuery($mysqli, $sqlFilePath, $tableName) {
    $result = $mysqli->query(file_get_contents($sqlFilePath));

    if ($result === false) {
        throw new Exception("Could not execute query for table: $tableName.");
    } else {
        print("Successfully ran SQL setup for $tableName.".PHP_EOL);
    }
}

try {
    $mysqli = new MySQLWrapper();

    // テーブル作成のSQLファイルを順番に実行
    runSqlQuery($mysqli, __DIR__ . '/users-setup.sql', 'users');
    runSqlQuery($mysqli, __DIR__ . '/posts-setup.sql', 'posts');
    runSqlQuery($mysqli, __DIR__ . '/post-like-setup.sql', 'post_like');
    runSqlQuery($mysqli, __DIR__ . '/comments-setup.sql', 'comments');
    runSqlQuery($mysqli, __DIR__ . '/comment-like-setup.sql', 'comment_like');
    runSqlQuery($mysqli, __DIR__ . '/user-settings-setup.sql', 'user_settings');
    runSqlQuery($mysqli, __DIR__ . '/tags-setup.sql', 'tags');
    runSqlQuery($mysqli, __DIR__ . '/post-tag-setup.sql', 'post_tag');

} catch (Exception $e) {
    print "Error: " . $e->getMessage() . PHP_EOL;
} finally {
    $mysqli->close();
}