<?php
// Start session to access session data (email)
session_start();

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "login"; // Replace with your database name

// Check if the email is stored in the session
if (!isset($_SESSION['email'])) {
    echo "User is not logged in. Please log in first.";
    exit; // Exit if the user is not logged in
}

$email = $_SESSION['email']; // Retrieve the email from the session

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $first_name = mysqli_real_escape_string($conn, $_POST['first-name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last-name']);

    // Check if a record with the same email exists
    $check_query = "SELECT * FROM user_data WHERE email = '$email'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        // Record exists, update the values based on email
        $update_query = "UPDATE user_data SET first_name = '$first_name', last_name = '$last_name' WHERE email = '$email'";
        if ($conn->query($update_query) === TRUE) {
            // Redirect to the next page after a successful update
            header("Location: ../work_as_engineer/work.html"); // Change "next_page.php" to your desired page
            exit; // Make sure to call exit after header to stop further execution
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // No record found, insert new values
        $insert_query = "INSERT INTO user_data (first_name, last_name, email) VALUES ('$first_name', '$last_name', '$email')";
        if ($conn->query($insert_query) === TRUE) {
            // Redirect to the next page after a successful insert
            header("Location: ../work_as_engineer/work.html"); // Change "next_page.php" to your desired page
            exit; // Make sure to call exit after header to stop further execution
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>
