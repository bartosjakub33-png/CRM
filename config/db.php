<?php
declare(strict_types=1);

/*
 * Uprav tieto údaje podľa svojej MySQL inštalácie.
 * DB_PASS = heslo používateľa, ktorého si nastavil pri inštalácii MySQL.
 */
const DB_HOST = 'localhost';
const DB_NAME = 'crm_gardens';
const DB_USER = 'crm_user';
const DB_PASS = 'silne_heslo';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
