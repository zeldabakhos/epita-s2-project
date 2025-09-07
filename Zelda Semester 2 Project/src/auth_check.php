<?php
session_start();
header('Content-Type: application/json');

$session_info = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'logged_in' => isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : 'Not Set',
    'session_data' => $_SESSION
];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); 
    echo json_encode(['error' => 'Only GET requests are allowed', 'debug' => $session_info]);
    exit;
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'debug' => $session_info]);
} else {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'User is authenticated', 'debug' => $session_info]);
}
?>
