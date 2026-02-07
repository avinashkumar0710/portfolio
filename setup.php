<?php
// This file generates the bcrypt hash for the default admin password
// Run this once to set up the database, then delete it

require_once 'config.php';

// Generate bcrypt hash for password "admin123"
$password = "admin123";
$hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Bcrypt hash for 'admin123': " . $hashed_password . "<br><br>";

// Check if admin_users table exists and create if not
$sql = "
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
";

if ($conn->query($sql) === TRUE) {
    echo "✓ admin_users table created successfully<br>";
} else {
    echo "✗ Error creating admin_users table: " . $conn->error . "<br>";
}

// Create admin_sessions table
$sql = "
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
)
";

if ($conn->query($sql) === TRUE) {
    echo "✓ admin_sessions table created successfully<br>";
} else {
    echo "✗ Error creating admin_sessions table: " . $conn->error . "<br>";
}

// Check if default admin user exists
$stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
$email = "admin@portfolio.com";
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert default admin user
    $stmt = $conn->prepare("INSERT INTO admin_users (email, password, name) VALUES (?, ?, ?)");
    $name = "Admin";
    $stmt->bind_param("sss", $email, $hashed_password, $name);
    
    if ($stmt->execute()) {
        echo "✓ Default admin user created<br>";
        echo "  Email: admin@portfolio.com<br>";
        echo "  Password: admin123<br>";
    } else {
        echo "✗ Error creating default admin user: " . $conn->error . "<br>";
    }
} else {
    echo "⚠ Default admin user already exists<br>";
}

echo "<br><strong>Setup complete!</strong> You can now delete this setup.php file.";
?>
