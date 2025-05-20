<?php
$host = "localhost";
$db_user = "postgres";  // Change if using a custom user
$db_pass = "123456";  // Set your PostgreSQL password
$db_name = "gym_system";
$port = "5432"; // Default PostgreSQL port
// Establish connection to PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$db_name user=$db_user password=$db_pass");

if (!$conn) {
    die("❌ Database connection failed: " . pg_last_error());
}

// Set UTF-8 encoding
pg_set_client_encoding($conn, 'UTF8');
?>