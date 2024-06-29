<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $previousGPA = floatval($_POST['cumulative_gpa']);
    $previousCredits = intval($_POST['completed_hours']);
    $subjects = $_POST['subjects'];

    $totalNewCredits = 0;
    $totalNewPoints = 0;

    foreach ($subjects as $subject) {
        $grade = floatval($subject['grade']);
        $credits = intval($subject['credits']);
        $oldGrade = isset($subject['oldGrade']) ? floatval($subject['oldGrade']) : null;

        if ($oldGrade !== null && $oldGrade > 0) {
            $totalNewPoints -= $oldGrade * $credits;
        }

        $totalNewPoints += $grade * $credits;
        $totalNewCredits += $credits;
    }

    $totalPoints = ($previousGPA * $previousCredits) + $totalNewPoints;
    $totalCredits = $previousCredits + $totalNewCredits;

    if ($totalCredits == 0) {
        $newGPA = 0;
    } else {
        $newGPA = $totalPoints / $totalCredits;
    }

    if ($totalNewCredits == 0) {
        $currentSemesterGPA = 0;
    } else {
        $currentSemesterGPA = $totalNewPoints / $totalNewCredits;
    }

    echo "المعدل التراكمي الجديد هو: " . number_format($newGPA, 2);
    echo "<br>المعدل للفصل الحالي: " . number_format($currentSemesterGPA, 2);
} else {
    echo "الرجاء استخدام النموذج لتقديم البيانات.";
}