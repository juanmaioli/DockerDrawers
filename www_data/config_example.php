<?php
	/**
	 * Drawers Configuration Example
	 * Copy this file to config.php and adjust the values.
	 */

	// Database configuration from environment variables or defaults
	$db_server = getenv('DB_SERVER') ?: "localhost";
	$db_user = getenv('DB_USER') ?: "root";
	$db_pass = getenv('DB_PASS') ?: "";
	$db_name = getenv('DB_NAME') ?: "drawers";
	$db_serverport = getenv('DB_PORT') ?: "3306";
	
	$table_pre = "drawers_";
	$www_host = "localhost";
	$www_https = "";
	$site_cookie = "drawersID";
	$contact_mail = "contact@example.com";

	/**
	 * Centralized database connection function
	 */
	function get_db_connection() {
		global $db_server, $db_user, $db_pass, $db_name, $db_serverport;
		$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$conn->set_charset("utf8");
		return $conn;
	}
?>
