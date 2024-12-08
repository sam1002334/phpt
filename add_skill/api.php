<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET");

$dsn = "mysql:host=localhost;dbname=catagories"; // Update with your database details
$username = "root"; // Update with your DB username
$password = ""; // Update with your DB password

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action === "getCategories") {
            // Fetch all categories
            try {
                $stmt = $pdo->prepare("SELECT * FROM categories");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($categories);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } elseif ($action === "getSkills" && isset($_GET['category_id'])) {
            // Fetch skills based on category ID
            $categoryId = intval($_GET['category_id']);
            try {
                $stmt = $pdo->prepare("SELECT * FROM skills WHERE category_id = ?");
                $stmt->execute([$categoryId]);
                $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($skills);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["error" => "Invalid action or missing parameters"]);
        }
    } else {
        echo json_encode(["error" => "No action specified"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
