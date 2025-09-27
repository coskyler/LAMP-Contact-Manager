<?php
// 1. Get the requested URL path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2. Simple routing logic
switch ($request_uri) {
    // API routes
    case '/api/auth/login':
    case '/api/auth/logout':
        $_GET['path'] = str_replace('/api/auth', '', $request_uri);
        require __DIR__ . '/api/auth.php';
        break;

    case '/api/users':
        require __DIR__ . '/api/users.php';
        break;

    case '/api/contacts':
        require __DIR__ . '/api/contacts.php';
        break;

    // Default to the main application page
    default:
        require __DIR__ . '/dashboard.php';
        break;
}
