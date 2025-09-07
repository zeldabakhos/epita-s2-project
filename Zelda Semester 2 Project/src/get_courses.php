<?php
require 'db.php';

function fetchCoursesData($discipline, $year, $period) {
    global $pdo;

    try {
        $sql = "SELECT DISTINCT c.course_code, 
                                c.course_name,
                                c.duration,
                                CONCAT(UPPER(SUBSTRING_INDEX(s.session_prof_ref, '.', 1)), ' ', 
                                       UPPER(SUBSTRING_INDEX(SUBSTRING_INDEX(s.session_prof_ref, '@', 1), '.', -1))) AS professor_name
                FROM students st
                JOIN grades g ON st.student_epita_email = g.grade_student_epita_email_ref
                JOIN courses c ON g.grade_course_code_ref = c.course_code
                JOIN sessions s ON s.session_course_ref = c.course_code
                WHERE st.student_population_code_ref = :discipline
                AND st.student_population_year_ref = :year
                AND st.student_population_period_ref = :period
                AND st.student_enrollment_status = 'completed'";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':discipline', $discipline);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':period', $period);
        $stmt->execute();

        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'courses' => $courses]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

if (isset($_POST['discipline']) && isset($_POST['year']) && isset($_POST['period'])) {
    fetchCoursesData($_POST['discipline'], $_POST['year'], $_POST['period']);
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
}
?>
