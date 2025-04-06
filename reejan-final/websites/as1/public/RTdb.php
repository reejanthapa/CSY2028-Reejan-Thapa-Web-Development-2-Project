<?php
$RTHost = 'mysql'; 
$RTDb = 'ijdb'; 
$RTUser = 'student';
$RTPass = 'student';
$RTCharset = 'utf8mb4';

$RTDsn = "mysql:host=$RTHost;dbname=$RTDb;charset=$RTCharset";

$RTOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $RTPdo = new PDO($RTDsn, $RTUser, $RTPass, $RTOptions);
} catch (PDOException $e) {
    die('RT Database Connection Failed: ' . $e->getMessage());
}
?>
