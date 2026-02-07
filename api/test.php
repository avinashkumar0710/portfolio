<?php
// Test script to diagnose login issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Login System Diagnosis</h2>";

// Test 1: Check if config.php can be loaded
echo "<h3>Test 1: Config File</h3>";
if (file_exists('../config.php')) {
    echo "✓ config.php exists<br>";
    require_once '../config.php';
    echo "✓ config.php loaded successfully<br>";
} else {
    echo "✗ config.php NOT found<br>";
    exit;
}

// Test 2: Check database connection
echo "<h3>Test 2: Database Connection</h3>";
if ($conn->connect_error) {
    echo "✗ Database connection failed: " . $conn->connect_error . "<br>";
    exit;
} else {
    echo "✓ Database connected successfully<br>";
}

// Test 3: Check if admin_users table exists
echo "<h3>Test 3: Database Tables</h3>";
$result = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($result && $result->num_rows > 0) {
    echo "✓ admin_users table exists<br>";
} else {
    echo "✗ admin_users table NOT found<br>";
}

$result = $conn->query("SHOW TABLES LIKE 'admin_sessions'");
if ($result && $result->num_rows > 0) {
    echo "✓ admin_sessions table exists<br>";
} else {
    echo "✗ admin_sessions table NOT found<br>";
}

// Test 4: Check if default user exists
echo "<h3>Test 4: Default Admin User</h3>";
$result = $conn->query("SELECT id, email, name FROM admin_users WHERE email = 'admin@portfolio.com'");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "✓ Default user exists: " . $user['email'] . "<br>";
    echo "  ID: " . $user['id'] . "<br>";
    echo "  Name: " . $user['name'] . "<br>";
} else {
    echo "✗ Default user NOT found<br>";
    echo "  Available users:<br>";
    $result = $conn->query("SELECT id, email, name FROM admin_users");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "    - " . $row['email'] . "<br>";
        }
    } else {
        echo "    (no users found)<br>";
    }
}

// Test 5: Test password hashing
echo "<h3>Test 5: Password Hashing</h3>";
$test_password = 'admin123';
$test_hash = password_hash($test_password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "Sample hash for 'admin123':<br>";
echo "<code>" . $test_hash . "</code><br><br>";

// Test 6: Check actual password in database
echo "<h3>Test 6: Stored Password Hash</h3>";
$result = $conn->query("SELECT password FROM admin_users WHERE email = 'admin@portfolio.com'");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $stored_hash = $user['password'];
    echo "Stored hash:<br>";
    echo "<code>" . $stored_hash . "</code><br><br>";
    
    // Test if password verifies
    if (password_verify('admin123', $stored_hash)) {
        echo "✓ Password 'admin123' verifies correctly<br>";
    } else {
        echo "✗ Password 'admin123' does NOT verify<br>";
        echo "The hash in the database is not correct for 'admin123'<br>";
        echo "<br><strong>To fix this, run this SQL:</strong><br>";
        $new_hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
        echo "<code>UPDATE admin_users SET password = '" . $new_hash . "' WHERE email = 'admin@portfolio.com';</code>";
    }
} else {
    echo "✗ Could not retrieve stored password<br>";
}

// Test 7: API endpoint test
echo "<h3>Test 7: API Endpoint</h3>";
if (file_exists('../api/auth.php')) {
    echo "✓ api/auth.php exists<br>";
} else {
    echo "✗ api/auth.php NOT found<br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>If all tests show ✓, the login system should work.</p>";
echo "<p>If any tests show ✗, follow the recommendations above to fix them.</p>";
?>
