<?php
// Falls back to local defaults if no environment variables are set,
// so this still works outside Docker without changes.
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'examplepass';
$DB_NAME = getenv('DB_NAME') ?: 'janus_website2';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}