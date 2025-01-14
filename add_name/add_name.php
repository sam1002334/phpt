<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login_page/login_page.html");
    exit();
}

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

// Get the user's email from the session
$email = $_SESSION['email'];

// Fetch user data from the signup_user table using the email
$stmt = $conn->prepare("SELECT profile_pic FROM signup_user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

// If user data is found, fetch it
if ($stmt->num_rows > 0) {
    $stmt->bind_result($profilePic);
    $stmt->fetch();
} else {
    die("User data not found.");
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Details - Easylife</title>
  <style>
    /* Global Styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f1f1f1;
      color: #333;
    }

    .header {
      background-color: #1a1a1a;
      color: #ffffff;
      padding: 20px 30px;
      font-size: 22px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 60px auto;
      max-width: 600px;
      background-color: #ffffff;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      font-size: 32px;
      margin-top: 20px;
      color: #ff6f61; /* Vibrant color for contrast */
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      position: relative;
      animation: text-animation 1s ease-in-out infinite alternate;
    }

    @keyframes text-animation {
      0% {
        color: #ff6f61;
        text-shadow: 2px 2px 5px rgba(255, 111, 97, 0.7);
      }
      100% {
        color: #4f94d4; /* Change color at the end of animation */
        text-shadow: 2px 2px 10px rgba(79, 148, 212, 0.7);
      }
    }

    p {
      color: #555;
      font-size: 18px;
      line-height: 1.6;
      background: linear-gradient(45deg, #f06, #4f94d4); /* Gradient background for text */
      -webkit-background-clip: text; /* Clips the background to text */
      color: transparent; /* Makes the text transparent to reveal the gradient */
      font-weight: 400;
      animation: paragraph-animation 2s ease-in-out infinite alternate;
      text-shadow: 0 0 5px rgba(255, 111, 97, 0.5), 0 0 10px rgba(255, 111, 97, 0.5);
    }

    @keyframes paragraph-animation {
      0% {
        text-shadow: 0 0 8px rgba(79, 148, 212, 0.6);
      }
      100% {
        text-shadow: 0 0 15px rgba(255, 111, 97, 0.8);
      }
    }


    .next-button {
      margin-top: 25px;
      padding: 12px 25px;
      font-size: 18px;
      color: #ffffff;
      background-color: #007bff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .next-button:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    .next-button:active {
      transform: translateY(2px);
    }

    .profile-pic {
      width: 200px;
      height: 200px;
      border: 4px solid #f1f1f1;
      border-radius: 50%;
      overflow: hidden;
      background-color: #e6e6e6;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .profile-pic img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .camera-icon {
      position: absolute;
      bottom: 10px;
      right: 10px;
      width: 50px;
      height: 50px;
      background-color: #ffffff;
      border: 2px solid #007bff;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .camera-icon:hover {
      background-color: #007bff;
      transform: translateY(-2px);
    }

    .camera-icon img {
      width: 22px;
      height: 22px;
    }

    form {
      margin-top: 30px;
      width: 100%;
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      font-size: 16px;
      margin-bottom: 8px;
      color: #333;
    }

    .form-group input {
      width: 100%;
      padding: 12px 20px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    .form-group input:focus {
      border-color: #007bff;
      outline: none;
      background-color: #ffffff;
    }

    @media (max-width: 768px) {
      .container {
        margin: 30px auto;
        padding: 20px;
        max-width: 100%;
      }
      
      .next-button {
        padding: 10px 20px;
        font-size: 16px;
      }

      .profile-pic {
        width: 150px;
        height: 150px;
      }

      .camera-icon {
        width: 45px;
        height: 45px;
        bottom: 5px;
        right: 5px;
      }
    }


  </style>
</head>
<body>
  <div class="header">
    Easylife
  </div>
  <div class="container">
  <div class="profile-pic" id="profile-pic" style="background-image: url('<?php echo $profilePic; ?>');">
    <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Photo" id="profile-img">
    <div class="camera-icon" id="camera-icon">
      <img src="camera.png" alt="Camera Icon">
    </div>
  </div>
  <h2>What is your name?</h2>
  <p>Please use your real name as this will be required for identity verification.</p>
  <form method="POST" action="process_form.php">
    <div class="form-group">
        <label for="first-name">First Name</label>
        <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required>
    </div>
    <div class="form-group">
        <label for="last-name">Last Name</label>
        <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required>
    </div>
    <button class="next-button" id="next-button">Next</button>
  </form>
</div>


  <form id="upload-form" method="POST" enctype="multipart/form-data" action="upload.php">
    <input type="file" name="profile_pic" id="file-input" accept="image/*" style="display: none;">
  </form>
  <!-- <script>
    // Handle Next button click
    document.getElementById('next-button').addEventListener('click', function () {
        // Redirect to the next page
        window.location.href = '../login_page/login_page.html'; // Replace 'next_page.html' with the actual next page URL
    });

  </script> -->
  <script src="profile.js">
  </script>
</body>
</html>
