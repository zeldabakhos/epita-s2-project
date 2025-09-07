<?php
require 'db.php';  
session_start(); 
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); 
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    $sql = "SELECT password FROM admin WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if (password_verify($password, $result['password'])) {
            $_SESSION['logged_in'] = true;
            http_response_code(200); 
            echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
        } else {
            http_response_code(401); 
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
        }
    } else {
        http_response_code(404); 
        echo json_encode(['status' => 'error', 'message' => 'No account found with this email.']);
    }
} else {
    http_response_code(400); 
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
