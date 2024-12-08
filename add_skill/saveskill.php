<?php
session_start();  // Start the session to access session variables

// Database connection details
$host = "localhost"; // Database host
$dbname = "login"; // Your database name
$username = "root"; // Your database username
$password = ""; // Your database password

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: POST");

// Ensure the user is logged in and email is available in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];  // Get email from the session

    // Fetch POST data (JSON body)
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['skills']) && is_array($inputData['skills'])) {
        // Fetch user_id based on email
        $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Now we have the user_id, let's insert skills into the database
            $stmt = $conn->prepare("INSERT INTO user_skills (user_id, skill_id) VALUES (?, ?)");

            foreach ($inputData['skills'] as $skill_id) {
                $stmt->bind_param("ii", $user_id, $skill_id);
                $stmt->execute();
            }

            // Success response
            echo json_encode(["success" => true]);
        } else {
            // User not found
            echo json_encode(["error" => "User not found"]);
        }

        $stmt->close();
    } else {
        // Invalid skills data
        echo json_encode(["error" => "Invalid skills data"]);
    }
} else {
    // User not logged in
    echo json_encode(["error" => "User not logged in"]);
}

$conn->close();
?>
