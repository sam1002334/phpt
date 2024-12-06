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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT password FROM user WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists, now fetch the stored password
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Compare the passwords directly
        if ($stored_password === $password) {
            // Password is correct, redirect to the main page
            header("Location: ../main/main_page.html");
            exit();
        } else {
            // Incorrect password
            header("Location: login_page.html?error=1");
            exit();
        }
    } else {
        // Email does not exist, redirect to signup page
        header("Location: ../signup_page/signup_page.html");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
