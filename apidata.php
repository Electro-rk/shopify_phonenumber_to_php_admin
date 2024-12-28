<?php

header('Content-Type: application/json');

// Database connection credentials
$host = 'localhost'; // Database host
$dbname = ''; // Database name
$username = ''; // Database username
$password = ''; // Database password

// Establish a database connection
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    http_response_code(500);
    exit;
}

// Read the incoming request data
$requestData = json_decode(file_get_contents('php://input'), true);
$mobile = isset($requestData['mobile']) ? mysqli_real_escape_string($conn, $requestData['mobile']) : null;
$ip = isset($requestData['ip']) ? mysqli_real_escape_string($conn, $requestData['ip']) : null;

// Validate inputs
if (!$mobile || !$ip) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    http_response_code(400);
    exit;
}

// Check if the IP address has already submitted
$query = "SELECT * FROM submissions WHERE ip_address = '$ip'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(['success' => false, 'message' => 'This IP has already submitted a number.']);
    http_response_code(400);
    exit;
}

// Insert the new submission into the database
$query = "INSERT INTO submissions (mobile_number, ip_address) VALUES ('$mobile', '$ip')";
if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Mobile number submitted successfully.']);
    http_response_code(200);
} else {
    echo json_encode(['success' => false, 'message' => 'Error inserting data: ' . mysqli_error($conn)]);
    http_response_code(500);
}

// Close the database connection
mysqli_close($conn);
