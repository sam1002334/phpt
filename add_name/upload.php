<?php
session_start();

// Check if user is logged in, if not, deny the upload
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$email = $_SESSION['email'];
$target_dir = "uploads/"; // Directory to store uploaded images
$imageFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

// Generate a unique file name to avoid overwriting existing files
$uniqueFileName = uniqid("profile_", true) . "." . $imageFileType;
$target_file = $target_dir . $uniqueFileName;

// Connect to the database
$conn = new mysqli("localhost", "root", "", "login");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Check if the user has an existing profile photo
$stmt = $conn->prepare("SELECT profile_pic FROM signup_user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($existingPhoto);
$stmt->fetch();
$stmt->close();

// If a previous photo exists, delete it
if ($existingPhoto && file_exists($existingPhoto)) {
    unlink($existingPhoto); // Deletes the file
}

// Validate if the file is an actual image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => 'File is not an image']);
        exit();
    }
}

// Check file size (e.g., 5MB limit)
if ($_FILES["file"]["size"] > 5000000) {
    echo json_encode(['success' => false, 'message' => 'Sorry, your file is too large']);
    exit();
}

// Allow only certain file formats (jpg, jpeg, png)
if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
    echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, and PNG files are allowed']);
    exit();
}

// Try to upload the file
if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    // File upload successful, now update the database with the new profile photo
    $stmt = $conn->prepare("UPDATE signup_user SET profile_pic = ? WHERE email = ?");
    $stmt->bind_param("ss", $target_file, $email);

    if ($stmt->execute()) {
        // Successfully updated profile photo
        echo json_encode(['success' => true, 'fileName' => $uniqueFileName]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file']);
}

$conn->close();
?>
