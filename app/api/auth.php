<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cookie.php';

$action = $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // If already logged in, redirect to contacts page
    if(checkAuthCookie() !== null) {
        header("Location: /contacts");
        exit;
    }

    $user   = trim($_POST['username'] ?? '');
    $pass   = $_POST['password'] ?? '';

    // Check for empty fields
    if ($user === '' || $pass === '') {
        echo "All fields are required";
        exit;
    }

    try {
        $pdo = db();

        // Check if username exists
        $stmt = $pdo->prepare("SELECT id, password FROM Users WHERE username = ? LIMIT 1");
        $stmt->execute([$user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo "Invalid username or password";
            exit;
        }

        if (!password_verify($pass, $row['password'])) {
            echo "Invalid username or password";
            exit;
        }

        setAuthCookie($row['id']);
        
        echo "OK";

    } catch(PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo "Database error";
    } catch (Throwable $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo "Server error";
    }
    exit;
}

if ($action === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteAuthCookie();

    header("Location: /");
    exit;
}
?>