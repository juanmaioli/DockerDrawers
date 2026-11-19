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

	// Mercado Libre API Configuration
	$ml_client_id = getenv('ML_CLIENT_ID') ?: "YOUR_ML_CLIENT_ID";
	$ml_client_secret = getenv('ML_CLIENT_SECRET') ?: "YOUR_ML_CLIENT_SECRET";
	$ml_redirect_uri = getenv('ML_REDIRECT_URI') ?: "https://localhost/compras.php";

	/**
	 * Centralized database connection function
	 */
	function get_db_connection() {
		global $db_server, $db_user, $db_pass, $db_name, $db_serverport;
		$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$conn->set_charset("utf8mb4");
		return $conn;
	}

	/**
	 * Ensures the user has an active authenticated session.
	 */
	function require_auth(bool $json = false) {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || empty($_SESSION['usuario_id'])) {
			if ($json) {
				http_response_code(401);
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(['error' => true, 'message' => 'No autorizado']);
			} else {
				header('Location: login.php');
			}
			exit();
		}
	}

	/**
	 * Ensures the authenticated user has administrator privileges (right == 1).
	 */
	function require_admin(bool $json = false) {
		require_auth($json);
		if ((int)($_SESSION['right'] ?? 0) !== 1) {
			if ($json) {
				http_response_code(403);
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(['error' => true, 'message' => 'Acceso denegado']);
			} else {
				header('Location: index.php');
			}
			exit();
		}
	}

	/**
	 * Validates the CSRF token from POST requests.
	 */
	function validate_csrf() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		$session_token = $_SESSION['csrf_token'] ?? '';
		$post_token = $_POST['csrf_token'] ?? '';
		if (empty($session_token) || empty($post_token) || !hash_equals($session_token, $post_token)) {
			header('HTTP/1.1 403 Forbidden');
			die("CSRF token validation failed.");
		}
	}
?>
