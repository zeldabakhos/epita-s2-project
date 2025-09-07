<?php
require 'db.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    try {
        $stmt = $pdo->prepare("SELECT DISTINCT course_code FROM courses");
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);

    } catch (PDOException $e) {
        echo json_encode(['error' => "Could not connect to the database or fetch data: " . $e->getMessage()]);
    }
}
?>
