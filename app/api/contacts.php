<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cookie.php';

header('Content-Type: application/json');

$userId = checkAuthCookie();

// If not logged in send home
if($userId === null) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => "Unauthorized"
    ]);
    exit;
}

//Create contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];

    $first_name     = trim($data['first_name'] ?? '');
    $last_name      = trim($data['last_name'] ?? '');
    $phone_number   = trim($data['phone_number'] ?? '');
    $email          = trim($data['email'] ?? '');
    $notes          = trim($data['notes'] ?? '');

    { //validate inputs
        // validate first name
        if ($first_name === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "First name cannot be empty"
            ]);
            exit;
        }
        if (strlen($first_name) > 50) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "First name cannot be more than 50 characters"
            ]);
            exit;
        }

        // validate last name
        if ($last_name === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Last name cannot be empty"
            ]);
            exit;
        }
        if (strlen($last_name) > 50) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Last name cannot be more than 50 characters"
            ]);
            exit;
        }

        // validate phone number
        if (strlen($phone_number) > 20) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Phone number cannot be more than 20 characters"
            ]);
            exit;
        }

        // validate email
        if (strlen($email) > 255) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Email cannot be more than 255 characters"
            ]);
            exit;
        }

        // validate notes
        if (strlen($notes) > 512) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Notes cannot be more than 512 characters"
            ]);
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
                ':ph' => $phone_number,
                ':em' => $email,
                ':nt' => $notes
            ]);
    } catch(PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Database error"
        ]);
        exit;
    }

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => "Contact created"
    ]);
    exit;
}

//Search contacts
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search  = trim($_GET['search'] ?? '');

    try {
        $pdo = db();

        if($search === '') {
            $stmt = $pdo->prepare("
                SELECT contact_id, first_name, last_name, phone_number, email, notes
                FROM Contacts
                WHERE user_id = ?
                ORDER BY contact_id DESC
                LIMIT 50
            ");
            $stmt->execute([$userId]);
        } else {
            $stmt = $pdo->prepare("
                SELECT contact_id, first_name, last_name, phone_number, email, notes
                FROM Contacts
                WHERE user_id = ?
                AND MATCH(name_search) AGAINST (? IN BOOLEAN MODE)
                ORDER BY contact_id DESC
                LIMIT 50
            ");
            $stmt->execute([$userId, $search]);
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


        echo json_encode([
            'success' => true,
            'message' => "Contacts fetched",
            'rows' => $rows
        ]);
        exit;
        
    } catch(PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Database error"
        ]);
        exit;
    }
}

//Update contact
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];
    
    $contact_id = (int)($data['contact_id'] ?? 0);
    if($contact_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Contact ID required"
        ]);
        exit;
    }

    $first_name     = isset($data['first_name'])    ? trim($data['first_name'])    : null;
    $last_name      = isset($data['last_name'])     ? trim($data['last_name'])     : null;
    $phone_number   = isset($data['phone_number'])  ? trim($data['phone_number'])  : null;
    $email          = isset($data['email'])         ? trim($data['email'])         : null;
    $notes          = isset($data['notes'])         ? trim($data['notes'])         : null;

    $toUpdate = [];
    $params = [':cid'=>$contact_id, ':uid'=>$userId];

    { //get & validate inputs
        // validate first name
        if($first_name !== null) {
            if (strlen($first_name) > 50) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "First name cannot be more than 50 characters"
                ]);
                exit;
            }
            $toUpdate[] = "first_name = :fn";
            $params[':fn'] = $first_name;
        }


        // validate last name
        if ($last_name !== null) {
            if (strlen($last_name) > 50) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Last name cannot be more than 50 characters"
                ]);
                exit;
            }
            $toUpdate[] = "last_name = :ln";
            $params[':ln'] = $last_name;
        }

        // validate phone number
        if ($phone_number !== null) {
            if (strlen($phone_number) > 20) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Phone number cannot be more than 20 characters"
                ]);
                exit;
            }
            $toUpdate[] = "phone_number = :ph";
            $params[':ph'] = $phone_number;
        }

        // validate email
        if ($email !== null) {
            if (strlen($email) > 255) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Email cannot be more than 255 characters"
                ]);
                exit;
            }
            $toUpdate[] = "email = :em";
            $params[':em'] = $email;
        }

        // validate notes
        if ($notes !== null) {
            if (strlen($notes) > 512) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Notes cannot be more than 512 characters"
                ]);
                exit;
            }
            $toUpdate[] = "notes = :nt";
            $params[':nt'] = $notes;
        }
    }

    if(!$toUpdate) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "No fields to update"
        ]);
        exit;
    }

    try {
        $pdo = db();

        $sql = "UPDATE Contacts SET ".implode(',',$toUpdate)." WHERE contact_id = :cid AND user_id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => "No change / Contact not found"
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => "Contact updated"
        ]);
        exit;

    } catch(PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Database error"
        ]);
        exit;
    }
}

// Delete contact
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];
    $contact_id = (int)($data['contact_id'] ?? 0);

    if ($contact_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Contact ID required"
        ]);
        exit;
    }

    try {
        $pdo = db();

        $stmt = $pdo->prepare("DELETE FROM Contacts WHERE contact_id = :cid AND user_id = :uid");
        $stmt->execute([':cid' => $contact_id, ':uid' => $userId]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => "Contact not found"
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => "Contact deleted"
        ]);
        exit;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => "Database error"
        ]);
        exit;
    }
}

?>