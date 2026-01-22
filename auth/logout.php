<?php
/**
 * Logout Logic
 * 
 * Destroys the session and redirects the user to the login page.
 * 
 * @package Auth
 */
session_start();
session_unset();
session_destroy();

header("Location: login.php");

exit();