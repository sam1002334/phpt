<?php
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

// Function to insert full user details into the signup_user table
function addFullUser($conn, $firstName, $lastName, $age, $gender, $email, $password) {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO signup_user (firstName, lastName, age, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssisss", $firstName, $lastName, $age, $gender, $email, $password);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: ../login_page/login_page.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Function to insert user credentials (email and password) into the user table
function addUserCredentials($conn, $email, $password) {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO user (email, password) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email already exists
        header("Location: ../login_page/login_page.html?error=1");
    } else {
        // Add user details and credentials without hashing the password
        addUserCredentials($conn, $email, $password);
        addFullUser($conn, $firstName, $lastName, $age, $gender, $email, $password);
        
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
