<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
require_once __DIR__ . '/../cookie.php';

$userId = checkAuthCookie();

// If not logged in send home
if($userId === null) {
    header("Location: /");
    exit;
}

//Create contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name  = trim($_POST['first_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $notes       = trim($_POST['notes'] ?? '');

    { //validate inputs
        // validate first name
        if ($first_name === '') {
            http_response_code(400);
            echo "First name cannot be empty";
            exit;
        }
        if (strlen($first_name) > 50) {
            http_response_code(400);
            echo "First name cannot be more than 50 characters";
            exit;
        }

        // validate last name
        if ($last_name === '') {
            http_response_code(400);
            echo "Last name cannot be empty";
            exit;
        }
        if (strlen($last_name) > 50) {
            http_response_code(400);
            echo "Last name cannot be more than 50 characters";
            exit;
        }

        // validate phone number
        if (strlen($phone) > 20) {
            http_response_code(400);
            echo "Phone number cannot be more than 20 characters";
            exit;
        }

        // validate email
        if (strlen($email) > 255) {
            http_response_code(400);
            echo "Email cannot be more than 255 characters";
            exit;
        }

        // validate notes
        if (strlen($notes) > 512) {
            http_response_code(400);
            echo "Notes cannot be more than 512 characters";
            exit;
        }
    }
    
    try {
        $pdo = db();

        $stmt = $pdo->prepare(
                "INSERT INTO Contacts (user_id, first_name, last_name, phone_number, email, notes)
                VALUES (:u, :fn, :ln, :ph, :em, :nt)"
            );
            $stmt->execute([
                ':u'  => $userId,
                ':fn' => $first_name,
                ':ln' => $last_name,
                ':ph' => $phone,
                ':em' => $email,
                ':nt' => $notes
            ]);
    } catch(PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo "Database error";
        exit;
    }

    echo "OK";
}
?>