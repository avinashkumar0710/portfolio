<?php
// Admin Authentication API with Database Fallback
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$response = ['success' => false, 'message' => 'Invalid request'];
$db_available = false;
$conn = null;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to load config and connect to database
if (file_exists('../config.php')) {
    try {
        require_once '../config.php';
        // Check if connection is valid
        if (isset($conn) && !$conn->connect_error) {
            $db_available = true;
        }
    } catch (Exception $e) {
        $db_available = false;
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        exit;
    }
    
    // Try database first, fall back to hardcoded credentials
    if ($db_available && $conn) {
        $stmt = $conn->prepare("SELECT id, email, password, name FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Create session
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['session_id'] = bin2hex(random_bytes(32));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'name' => $user['name']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } else {
        // Database not available - use fallback credentials for testing
        if ($email === 'admin@portfolio.com' && $password === 'admin123') {
            $_SESSION['admin_user_id'] = 1;
            $_SESSION['admin_email'] = 'admin@portfolio.com';
            $_SESSION['admin_name'] = 'Admin';
            $_SESSION['session_id'] = bin2hex(random_bytes(32));
            $_SESSION['offline_mode'] = true;
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => 1,
                    'email' => 'admin@portfolio.com',
                    'name' => 'Admin'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    }
    exit;
}

// Handle check auth
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check_auth') {
    if (isset($_SESSION['admin_user_id'])) {
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => $_SESSION['admin_user_id'],
                'email' => $_SESSION['admin_email'],
                'name' => $_SESSION['admin_name']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'authenticated' => false,
            'message' => 'Not authenticated'
        ]);
    }
    exit;
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit;
}

echo json_encode($response);
?>
