<?php

function getDB($dsn = null, $username = null, $password = null) {
    $dsn = $dsn ?? getenv("DB");
    $username = $username ?? getenv("DB_USER");
    $password = $password ?? getenv("DB_PASS");
    return new \PDO($dsn, $username, $password);
}