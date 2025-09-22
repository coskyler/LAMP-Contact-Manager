<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cookie.php';

// Create account
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first  = trim($_POST['first_name'] ?? '');
    $last   = trim($_POST['last_name'] ?? '');
    $user   = trim($_POST['username'] ?? '');
    $pass   = $_POST['password'] ?? '';
    $verify = $_POST['verify_password'] ?? '';

    // Check for empty fields
    if ($first === '' || $last === '' || $user === '' || $pass === '' || $verify === '') {
        echo "All fields are required";
        exit;
    }

    // Check username length
    if (strlen($user) < 3) {
        echo "Username must be at least 3 characters";
        exit;
    }

    // Check password length
    if (strlen($pass) < 8) {
        echo "Password must be at least 8 characters";
        exit;
    }

    // Check password match
    if ($pass !== $verify) {
        echo "Passwords do not match";
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
        
        echo "OK";

    } catch (PDOException $e) {
        $sqlstate   = $e->getCode();
        $driverCode = $e->errorInfo[1] ?? null;

        if($sqlstate === '23000' && $driverCode === 1062) {
            echo "Username already exists";
            exit;
        } else {
            error_log($e->getMessage());
            http_response_code(500);
            echo "Database error";
        }
    } catch (Throwable $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo "Server error";
    }
}
?>