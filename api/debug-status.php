<?php
// Quick debug endpoint to check what's happening
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

$output = [
    'timestamp' => date('Y-m-d H:i:s'),
    'session_status' => session_status(),
    'session_name' => session_name(),
    'session_id' => session_id(),
    'php_version' => phpversion(),
    'user_id_isset' => isset($_SESSION['user_id']),
];

// Check if contacts table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'contacts'");
$output['contacts_table_exists'] = $tableCheck->num_rows > 0;

// If table exists, count records
if ($output['contacts_table_exists']) {
    $countResult = $conn->query("SELECT COUNT(*) as count FROM contacts");
    $countRow = $countResult->fetch_assoc();
    $output['contact_messages_count'] = $countRow['count'];
    
    // Get first few messages
    $messagesResult = $conn->query("SELECT id, first_name, email, subject, created_at FROM contacts ORDER BY created_at DESC LIMIT 5");
    $messages = [];
    while ($row = $messagesResult->fetch_assoc()) {
        $messages[] = $row;
    }
    $output['recent_messages'] = $messages;
}

// Check database
$output['database_name'] = DB_NAME;
$output['database_connected'] = !$conn->connect_error;

echo json_encode($output, JSON_PRETTY_PRINT);
?>
