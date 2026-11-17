<?php
/* =========================================================
   STUDENT READING ASSIGNMENT ACCESS VALIDATION
   ========================================================= */

function studentHasActiveReadingAssignment(mysqli $mysqli, string $studentId, int $assignmentId, int $readingActivityId): bool
{
    $sql = "
        SELECT a.id
        FROM class_code_reading_assignments a
        INNER JOIN class_codes cc ON cc.id = a.class_code_id
        INNER JOIN academic_year_semesters ays ON ays.id = cc.academic_year_semester_id
        INNER JOIN class_code_enrollments cce
            ON cce.class_code_id = cc.id
            AND cce.student_id = ?
            AND cce.status = 'Accepted'
        INNER JOIN reading_activity_table r ON r.id = a.reading_activity_id
        WHERE a.id = ?
          AND a.reading_activity_id = ?
          AND a.status = 'Active'
          AND cc.status = 'Active'
          AND ays.status = 'Active'
          AND (r.status = 'active' OR r.status IS NULL OR r.status = '')
        LIMIT 1
    ";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("sii", $studentId, $assignmentId, $readingActivityId);
    $stmt->execute();
    $found = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $found;
}
