<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$dataFile = __DIR__ . '/../data/portfolio-data.json';
$dataDir = dirname($dataFile);

// Create data directory if it doesn't exist
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Default data structure
$defaultData = [
    'about' => [
        'name' => 'Avinash',
        'role' => 'Full Stack Developer & Data Analyst',
        'location' => 'Ahiwara, Durg',
        'bio' => 'Based in Ahiwara, Durg, I specialize in building custom digital solutions that drive business efficiency. Currently working at NSPCL - NTPC Ltd.',
        'image' => ''
    ],
    'projects' => [
        ['id' => 1, 'title' => 'Online Training Management System', 'category' => 'web', 'description' => 'Enterprise training platform', 'tech' => ['PHP', 'SQL Server']],
        ['id' => 2, 'title' => 'Online Complaint Management System', 'category' => 'web', 'description' => 'Complaint tracking system', 'tech' => ['PHP', 'SQL Server']],
        ['id' => 3, 'title' => 'NSPCL Mobile Application', 'category' => 'mobile', 'description' => 'Mobile app for internal use', 'tech' => ['Ionic', 'Angular']],
        ['id' => 4, 'title' => 'Pizza Sales Power BI Report', 'category' => 'data', 'description' => 'Data visualization project', 'tech' => ['Power BI', 'DAX']]
    ],
    'skills' => [
        ['id' => 1, 'name' => 'PHP', 'category' => 'Languages', 'level' => 95],
        ['id' => 2, 'name' => 'JavaScript', 'category' => 'Languages', 'level' => 85],
        ['id' => 3, 'name' => 'SQL Server', 'category' => 'Databases', 'level' => 90],
        ['id' => 4, 'name' => 'Ionic', 'category' => 'Frameworks', 'level' => 80]
    ],
    'experience' => [
        ['id' => 1, 'title' => 'Web Developer', 'company' => 'NSPCL - NTPC Ltd', 'duration' => 'April 2022 - Present', 'description' => 'Enterprise solutions']
    ],
    'testimonials' => [
        ['id' => 1, 'text' => 'Great developer', 'author' => 'HR Manager', 'company' => 'NSPCL - NTPC Ltd']
    ],
    'certificates' => [
        ['id' => 1, 'title' => 'Data Analytics Consulting', 'issuer' => 'KPMG Australia Virtual Internship', 'link' => '', 'date' => ''],
        ['id' => 2, 'title' => 'Sales Forecasting', 'issuer' => 'HP LIFE Certification', 'link' => '', 'date' => ''],
        ['id' => 3, 'title' => 'Power BI Data Visualization', 'issuer' => 'Jobaaj.com', 'link' => '', 'date' => ''],
        ['id' => 4, 'title' => 'ChatGPT for Beginners V2', 'issuer' => 'Great Learning', 'link' => '', 'date' => ''],
        ['id' => 5, 'title' => 'Generative AI Foundations', 'issuer' => 'upGrad Certification', 'link' => '', 'date' => '']
    ],
    'contact' => [
        'email' => 'contact@avinash.dev',
        'phone' => '',
        'linkedin' => '',
        'github' => '',
        'twitter' => '',
        'location' => 'Ahiwara, Durg, Chhattisgarh, India'
    ],
    'customSections' => []
];

// Handle GET request - retrieve data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($dataFile)) {
        $data = json_decode(file_get_contents($dataFile), true);
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        // Return default data if file doesn't exist
        echo json_encode(['success' => true, 'data' => $defaultData]);
    }
    exit;
}

// Handle POST request - save data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    // Save to file
    if (file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
        echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save data']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>
