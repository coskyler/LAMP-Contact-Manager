<?php
function db(): PDO {
	static $pdo = null;
	if ($pdo) return $pdo;

	$dsn  = getenv('MYSQL_DSN');
	$user = getenv('MYSQL_USER');
	$pass = getenv('MYSQL_PASSWORD');

	$pdo = new PDO($dsn, $user, $pass, [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);
	return $pdo;
}