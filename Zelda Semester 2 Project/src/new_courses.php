<?php
require 'db.php';  

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $courseName = trim(htmlspecialchars($_POST['courseName'] ?? '', ENT_QUOTES, 'UTF-8'));
    $sessionCount = filter_input(INPUT_POST, 'sessionCount', FILTER_SANITIZE_NUMBER_INT);
    $teacherName = trim(htmlspecialchars($_POST['teacherName'] ?? '', ENT_QUOTES, 'UTF-8'));

    if (!$courseName || !$sessionCount || !$teacherName) {
        throw new Exception('All fields are required. Please ensure no fields are missing.');
    }

    $checkCourseSql = "SELECT COUNT(*) FROM courses WHERE course_name = :courseName";
    $checkCourseStmt = $pdo->prepare($checkCourseSql);
    $checkCourseStmt->bindParam(':courseName', $courseName);
    $checkCourseStmt->execute();
    $courseExists = $checkCourseStmt->fetchColumn();

    if ($courseExists > 0) {
        throw new Exception('A course with this name is already registered.');
    }

    $sql = "INSERT INTO courses (course_name, session_count, teacher_name) 
            VALUES (:courseName, :sessionCount, :teacherName)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':courseName', $courseName);
    $stmt->bindParam(':sessionCount', $sessionCount);
    $stmt->bindParam(':teacherName', $teacherName);
    
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'New course added successfully.']);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
