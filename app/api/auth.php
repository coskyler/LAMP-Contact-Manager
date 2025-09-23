<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cookie.php';

header('Content-Type: application/json');

$path = $_SERVER['PATH_INFO'] ?? '';

if ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];

    $user = trim($data['username'] ?? '');
    $pass = $data['password'] ?? '';

    // Check for empty fields
    if ($user === '' || $pass === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "All fields are required"
        ]);
        exit;
    }

    try {
        $pdo = db();

        // Check if username exists
        $stmt = $pdo->prepare("SELECT id, password FROM Users WHERE username = ? LIMIT 1");
        $stmt->execute([$user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => "Invalid username or password"
            ]);
            exit;
        }

        if (!password_verify($pass, $row['password'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => "Invalid username or password"
            ]);
            exit;
        }

        setAuthCookie($row['id']);
        
        echo json_encode([
            'success' => true,
            'message' => "Logged in"
        ]);

    } catch(PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Database error"
        ]);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Server error"
        ]);
    }
    exit;
}

if ($path === '/logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteAuthCookie();

    echo json_encode([
        'success' => true,
        'message' => "Logged out"
    ]);
    exit;
}
?>