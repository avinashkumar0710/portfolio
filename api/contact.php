<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start session at the beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

// Debug endpoint to check session
if (isset($_GET['debug'])) {
    echo json_encode([
        'session_status' => session_status(),
        'session_id' => session_id(),
        'user_id_set' => isset($_SESSION['user_id']),
        'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
        'session_data' => $_SESSION
    ]);
    exit;
}

// Handle POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $subscribe = isset($_POST['subscribe']) ? 1 : 0;

    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Check if contacts table exists, if not create it
    $tableCheck = $conn->query("SHOW TABLES LIKE 'contacts'");
    if ($tableCheck->num_rows === 0) {
        $createTable = "CREATE TABLE contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255) NOT NULL,
            message LONGTEXT NOT NULL,
            subscribe BOOLEAN DEFAULT 0,
            user_ip VARCHAR(45),
            user_agent VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            admin_notes LONGTEXT,
            INDEX idx_email (email),
            INDEX idx_created_at (created_at),
            INDEX idx_status (status)
        )";
        
        if (!$conn->query($createTable)) {
            echo json_encode(['success' => false, 'message' => 'Database table creation failed']);
            exit;
        }
    }

    // Sanitize input
    $firstName = $conn->real_escape_string($firstName);
    $lastName = $conn->real_escape_string($lastName);
    $email = $conn->real_escape_string($email);
    $phone = $conn->real_escape_string($phone);
    $subject = $conn->real_escape_string($subject);
    $message = $conn->real_escape_string($message);
    $userIp = $_SERVER['REMOTE_ADDR'];
    $userAgent = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT']);

    // Insert into database
    $sql = "INSERT INTO contacts (first_name, last_name, email, phone, subject, message, subscribe, user_ip, user_agent) 
            VALUES ('$firstName', '$lastName', '$email', '$phone', '$subject', '$message', $subscribe, '$userIp', '$userAgent')";

    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully! We will get back to you soon.',
            'contactId' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save message: ' . $conn->error]);
    }
    exit;
}

// Handle GET request (fetch messages for admin - requires authentication)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if user is authenticated (admin only) - check both possible session keys
    if (!isset($_SESSION['admin_user_id']) && !isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }

    // Check if contacts table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'contacts'");
    if ($tableCheck->num_rows === 0) {
        echo json_encode(['success' => true, 'data' => [], 'message' => 'No messages yet']);
        exit;
    }

    $sql = "SELECT id, first_name, last_name, email, phone, subject, message, created_at, status FROM contacts ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result) {
        $contacts = [];
        while ($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $contacts, 'count' => count($contacts)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch contacts: ' . $conn->error]);
    }
    exit;
}

// Handle other methods
else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>
