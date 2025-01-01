<?php
include '../config/database.php';
session_start();
if (!isset($_SESSION['admin'])) {
    die('You do not have permission to access this page. Please log in.');
}
?>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Username</th>
                <th scope="col">Correct Answers</th>
                <th scope="col">Wrong Answers</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch exam results for users
            $stmt = $db->prepare("SELECT u.username, SUM(a.is_correct) AS correct_count, 
                                  COUNT(a.is_correct) - SUM(a.is_correct) AS wrong_count 
                                  FROM user_answers a
                                  JOIN users u ON a.user_id = u.id
                                  WHERE a.exam_id = :exam_id 
                                  GROUP BY u.username");
            $stmt->execute([':exam_id' => 1]); // Here, exam ID is set to 1, can be modified
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add results to the table
            foreach ($results as $result) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($result['username']) . "</td>";
                echo "<td>" . htmlspecialchars($result['correct_count']) . "</td>";
                echo "<td>" . htmlspecialchars($result['wrong_count']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
