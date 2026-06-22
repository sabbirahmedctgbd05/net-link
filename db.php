<?php
declare(strict_types=1);

/* Database Configuration */

date_default_timezone_set('Asia/Dhaka');

$host = getenv('DB_HOST') ?: '';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    exit('Database connection error. Please try again later.');
}