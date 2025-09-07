<?php
require 'db.php';  

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contactRef = filter_input(INPUT_POST, 'contactRef', FILTER_SANITIZE_EMAIL);
    $firstName = trim(htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES, 'UTF-8'));
    $lastName = trim(htmlspecialchars($_POST['lastName'] ?? '', ENT_QUOTES, 'UTF-8'));
    $address = trim(htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8'));
    $city = trim(htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES, 'UTF-8'));
    $country = trim(htmlspecialchars($_POST['country'] ?? '', ENT_QUOTES, 'UTF-8'));
    $birthdate = trim(htmlspecialchars($_POST['birthdate'] ?? '', ENT_QUOTES, 'UTF-8')); // assuming YYYY-MM-DD format
    $populationPeriodRef = trim(htmlspecialchars($_POST['populationPeriodRef'] ?? '', ENT_QUOTES, 'UTF-8'));
    $populationYearRef = filter_input(INPUT_POST, 'populationYearRef', FILTER_SANITIZE_NUMBER_INT);
    $populationCodeRef = trim(htmlspecialchars($_POST['populationCodeRef'] ?? '', ENT_QUOTES, 'UTF-8'));

    if (!$email || !$contactRef || !$firstName || !$lastName || !$address || !$city || !$country || !$birthdate || !$populationPeriodRef || !$populationYearRef || !$populationCodeRef) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required. Please ensure no fields are missing.']);
        exit;
    }

    $checkUserSql = "SELECT COUNT(*) FROM students WHERE student_epita_email = :email";
    $checkUserStmt = $pdo->prepare($checkUserSql);
    $checkUserStmt->bindParam(':email', $email);
    $checkUserStmt->execute();
    $userExists = $checkUserStmt->fetchColumn();

    if ($userExists > 0) {
        echo json_encode(['status' => 'error', 'message' => 'A user with this email is already registered.']);
        exit;
    }

    $checkContactSql = "SELECT COUNT(*) FROM contacts WHERE contact_email = :contactRef";
    $checkContactStmt = $pdo->prepare($checkContactSql);
    $checkContactStmt->bindParam(':contactRef', $contactRef);
    $checkContactStmt->execute();
    $contactExists = $checkContactStmt->fetchColumn();

    if ($contactExists == 0) {
        $insertContactSql = "INSERT INTO contacts (contact_email, contact_first_name, contact_last_name, contact_address, contact_city, contact_country, contact_birthdate) 
                             VALUES (:contactRef, :firstName, :lastName, :address, :city, :country, :birthdate)";
        $insertContactStmt = $pdo->prepare($insertContactSql);
        $insertContactStmt->bindParam(':contactRef', $contactRef);
        $insertContactStmt->bindParam(':firstName', $firstName);
        $insertContactStmt->bindParam(':lastName', $lastName);
        $insertContactStmt->bindParam(':address', $address);
        $insertContactStmt->bindParam(':city', $city);
        $insertContactStmt->bindParam(':country', $country);
        $insertContactStmt->bindParam(':birthdate', $birthdate);

        try {
            $insertContactStmt->execute();
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error during contact insert: ' . $e->getMessage()]);
            exit;
        }
    }

    $sql = "INSERT INTO students (student_epita_email, student_contact_ref, student_population_period_ref, student_population_year_ref, student_population_code_ref, student_enrollment_status) 
            VALUES (:email, :contactRef, :populationPeriodRef, :populationYearRef, :populationCodeRef, 'selected')";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactRef', $contactRef);
        $stmt->bindParam(':populationPeriodRef', $populationPeriodRef);
        $stmt->bindParam(':populationYearRef', $populationYearRef);
        $stmt->bindParam(':populationCodeRef', $populationCodeRef);

        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Signup successful.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error during signup: ' . $e->getMessage()]);
    }
}
?>
