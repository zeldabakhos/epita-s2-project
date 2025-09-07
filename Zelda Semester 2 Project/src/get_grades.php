<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $course_code = isset($_GET['course_code']) ? $_GET['course_code'] : null;

    if ($course_code) {
        try {
            $query = "
                SELECT 
                    ROW_NUMBER() OVER (ORDER BY s.student_epita_email) AS id,
                    s.student_epita_email AS email,
                    c.contact_first_name AS f_name,
                    c.contact_last_name AS l_name,
                    co.course_name AS course,
                    ROUND(AVG(g.grade_score), 2) AS grade_out_of_20
                FROM 
                    grades g
                JOIN 
                    students s ON g.grade_student_epita_email_ref = s.student_epita_email
                JOIN 
                    contacts c ON s.student_contact_ref = c.contact_email
                JOIN 
                    courses co ON g.grade_course_code_ref = co.course_code 
                               AND g.grade_course_rev_ref = co.course_rev
                WHERE 
                    g.grade_course_code_ref = :course_code
                GROUP BY 
                    s.student_epita_email, c.contact_first_name, c.contact_last_name, co.course_name;
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':course_code', $course_code, PDO::PARAM_STR);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($results)) {
                echo json_encode(["success" => true, "grades" => $results]);
            } else {
                echo json_encode(["success" => false, "error" => "No data found."]);
            }

        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing course code"]);
    }
}

