<?php
include './config/database.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has already completed the exam
$stmt = $db->prepare("SELECT COUNT(*) FROM user_answers WHERE user_id = :user_id AND exam_id = 1 AND exam_completed = 1");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$examCompleted = $stmt->fetchColumn();

if ($examCompleted) {
    echo "<div class='alert alert-info text-center mt-4'>You have already completed this exam. You cannot retake it.</div>";
    exit();
}

// Fetch active exam data from the database
$stmt = $db->prepare("SELECT id, end_time, data FROM exam WHERE id=1");
$stmt->execute();
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    echo "No active exam found.";
    exit();
}

$endTime = strtotime($exam['end_time']);
$currentTime = time();

// Check if the exam time has expired
if ($currentTime > $endTime) {
    echo "<div class='alert alert-danger text-center mt-4'>The exam time has expired. You missed it.</div>";
    exit();
}

// Decode the exam data from JSON
$data = json_decode($exam['data'], true);

// Handle the POST request to submit answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($currentTime > $endTime) {
        echo "<div class='alert alert-danger text-center mt-4'>You submitted the exam late. Your answers were not accepted.</div>";
        exit();
    }

    $correctAnswers = 0;
    $totalQuestions = count($data);
    $userId = $_SESSION['user_id'];

    foreach ($data as $index => $questionData) {
        $selectedAnswer = $_POST['question_' . $index] ?? null;
        $correctAnswer = $questionData['answers']['is_correct'];
        $isCorrect = ($selectedAnswer === $correctAnswer) ? 1 : 0;

        // Save answers to the database
        $stmt = $db->prepare("INSERT INTO user_answers (user_id, exam_id, question_id, answer, is_correct, exam_completed) VALUES (:user_id, :exam_id, :question_id, :answer, :is_correct, :exam_completed)");
        $stmt->execute([
            ':user_id' => $userId,
            ':exam_id' => $exam['id'],
            ':question_id' => $index + 1,
            ':answer' => $selectedAnswer,
            ':is_correct' => $isCorrect,
            ':exam_completed' => 1
        ]);

        if ($isCorrect) {
            $correctAnswers++;
        }
    }

    echo "<div class='alert alert-success text-center mt-4'>You answered {$correctAnswers} out of {$totalQuestions} questions correctly!</div>";
    exit();
}
?>

<main class="main container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Exam</h2>
            <div id="time" class="alert alert-warning text-center"></div>
            <form id="examForm" method="POST" action="">
                <?php foreach ($data as $index => $questionData): ?>
                    <div class="card question-card mb-3">
                        <div class="card-header">
                            <h5><?php echo $questionData['question']; ?></h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($questionData['answers'] as $key => $answer): ?>
                                <?php if ($key !== 'is_correct'): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="question_<?php echo $index; ?>" value="<?php echo $key; ?>" id="question_<?php echo $index . '_' . $key; ?>">
                                        <label class="form-check-label" for="question_<?php echo $index . '_' . $key; ?>">
                                            <?php echo $answer; ?>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">Submit Exam</button>
            </form>
        </div>
    </div>
</main>

<script>
    // Timer class for client-side countdown
    class Timer {
        constructor(endTime, displayElement) {
            this.endTime = endTime;
            this.displayElement = displayElement;
        }

        updateTimer() {
            const now = new Date().getTime();
            const timeLeft = this.endTime - now;

            if (timeLeft <= 0) {
                this.displayElement.innerHTML = 'Time is up!';
                document.getElementById('examForm').submit(); // Automatically submit the form
                return;
            }

            const minutes = Math.floor(timeLeft / 60000);
            const seconds = Math.floor((timeLeft % 60000) / 1000);
            this.displayElement.innerHTML = `${minutes} minutes ${seconds} seconds remaining`;

            setTimeout(() => this.updateTimer(), 1000);
        }

        start() {
            this.updateTimer();
        }
    }

    const timer = new Timer(<?= $endTime * 1000 ?>, document.getElementById('time'));
    timer.start();
</script>
