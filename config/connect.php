<?php
/**
 * Database Connection Configuration
 * 
 * Establish a connection to the MySQL database.
 * 
 * @package Config
 */

/**
 * @var string $host Database host
 */
$host = "localhost";

/**
 * @var string $username Database username
 */
$username = "root";

/**
 * @var string $password Database password
 */
$password = "admin";

/**
 * @var string $db_name Database name
 */
$db_name = "workshop_aslab";

/**
 * @var mysqli $conn MySQLi connection object
 */
$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
