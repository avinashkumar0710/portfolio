<?php
/**
 * Quick test to verify contact messages are being stored
 * Visit: http://localhost/portfolio/test-contacts.php
 */

require_once __DIR__ . '/config.php';

echo "<h1>Contact Messages Debug</h1>";

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'contacts'");
echo "<h2>Table Status</h2>";
if ($tableCheck->num_rows > 0) {
    echo "<p style='color: green;'>✓ Contacts table EXISTS</p>";
} else {
    echo "<p style='color: orange;'>⚠ Contacts table DOES NOT EXIST (will be created on first submission)</p>";
}

// Count messages
if ($tableCheck->num_rows > 0) {
    $countResult = $conn->query("SELECT COUNT(*) as count FROM contacts");
    $countRow = $countResult->fetch_assoc();
    echo "<h2>Message Count</h2>";
    echo "<p>Total messages: <strong>" . $countRow['count'] . "</strong></p>";
    
    if ($countRow['count'] > 0) {
        echo "<h2>Recent Messages</h2>";
        $result = $conn->query("SELECT id, first_name, last_name, email, subject, created_at, status FROM contacts ORDER BY created_at DESC");
        echo "<table border='1' cellpadding='10' style='width:100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['subject'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Session info
echo "<h2>Session Information</h2>";
echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "ACTIVE" : "INACTIVE") . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>User ID in Session: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NOT SET") . "</p>";

// Database info
echo "<h2>Database Information</h2>";
echo "<p>Database: " . DB_NAME . "</p>";
echo "<p>Host: " . DB_HOST . "</p>";
echo "<p>Connected: " . ($conn->connect_error ? "NO - " . $conn->connect_error : "YES") . "</p>";

$conn->close();
?>
<style>
body { font-family: Arial; margin: 20px; }
h1, h2 { color: #333; }
table { border-collapse: collapse; }
td, th { text-align: left; }
</style>
