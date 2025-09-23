<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cookie.php';

header('Content-Type: application/json');

// Create account
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];

    $first  = trim($data['first_name'] ?? '');
    $last   = trim($data['last_name'] ?? '');
    $user   = trim($data['username'] ?? '');
    $pass   = $data['password'] ?? '';
    $verify = $data['verify_password'] ?? '';

    // Check for empty fields
    if ($first === '' || $last === '' || $user === '' || $pass === '' || $verify === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "All fields are required"
        ]);
        exit;
    }

    // Check username length
    if (strlen($user) < 3) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Username must be at least 3 characters"
        ]);
        exit;
    }

    if (strlen($user) > 50) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Username cannot be more than 50 characters"
        ]);
        exit;
    }

    // Check first name length
    if (strlen($first) > 50) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "First name cannot be more than 50 characters"
        ]);
        exit;
    }

    //Check last name length
    if (strlen($last) > 50) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Last name cannot be more than 50 characters"
        ]);
        exit;
    }

    // Check password length
    if (strlen($pass) < 8) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Password must be at least 8 characters"
        ]);
        exit;
    }

    if (strlen($pass) > 255) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Password cannot be more than 255 characters"
        ]);
        exit;
    }

    // Check password match
    if ($pass !== $verify) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Passwords do not match"
        ]);
        exit;
    }

    try {
        $pdo = db();

        // Insert new user
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "INSERT INTO Users (first_name, last_name, username, password)
             VALUES (:f, :l, :u, :p)"
        );
        $stmt->execute([':f'=>$first, ':l'=>$last, ':u'=>$user, ':p'=>$hash]);

        $userId = $pdo->lastInsertId(); // Get user id of new user
        setAuthCookie($userId); // Log the client in
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => "Account created"
        ]);

    } catch (PDOException $e) {
        $sqlstate   = $e->getCode();
        $driverCode = $e->errorInfo[1] ?? null;

        if($sqlstate === '23000' && $driverCode === 1062) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => "Username already exists"
            ]);
            exit;
        } else {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => "Database error"
            ]);
        }
    } catch (Throwable $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Server error"
        ]);
    }
}
?>