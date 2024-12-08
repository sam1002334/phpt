<?php
// Database connection
$host = "localhost";
$db_name = "login";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Insert form data into the database
    $sql = "INSERT INTO contact_form (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);

    try {
        $stmt->execute();

        // After storing in the database, redirect the user to Web3Forms API
        $web3form_url = 'https://api.web3forms.com/submit';
        $access_key = 'b47f4da1-cee0-47c9-9907-9c706eed0f5d';

        $data = [
            'access_key' => $access_key,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        // Send the data to Web3Forms API via cURL
        $ch = curl_init($web3form_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        // Check if the request was successful
        if ($response === false) {
            die("Error sending data to Web3Forms API.");
        }

        // Optionally, redirect the user to a "Thank You" page
        header("Location: thank-you.html");
        exit();

    } catch (PDOException $e) {
        die("Error saving data to the database: " . $e->getMessage());
    }
}
?>
