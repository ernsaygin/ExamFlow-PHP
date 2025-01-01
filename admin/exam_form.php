<?php
include '../config/database.php';

session_start();
if(!isset($_SESSION['admin'])) {
    die('You do not have permission to access this page. Please log in.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questions = $_POST['question'];
    $answers = $_POST['answers'];
    $duration = $_POST['duration'];

    // Calculate exam end time
    $now = new DateTime();
    $now->modify("+$duration minutes");
    $end_time = $now->format('Y-m-d H:i:s');

    // Convert questions and answers to JSON format
    $exam_data = [];
    foreach ($questions as $index => $question_text) {
        $answer_data = [];
        foreach ($answers[$index] as $key => $answer_text) {
            $answer_data[$key] = $answer_text;
        }

        $exam_data[] = [
            'question' => $question_text,
            'answers' => $answer_data
        ];
    }

    $exam_data_json = json_encode($exam_data);

    // Check for JSON encoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON encoding error: ' . json_last_error_msg();
        exit();
    }

    // Add the exam to the database
    $db->query('DELETE FROM user_answers');
    $db->query('DELETE FROM exam WHERE id = 1');
    $stmt = $db->prepare('INSERT INTO exam (id, data, duration, end_time) VALUES (?, ?, ?, ?)');
    $stmt->bindValue(1, 1, PDO::PARAM_INT); // ID is fixed, change to dynamic value if necessary
    $stmt->bindValue(2, $exam_data_json, PDO::PARAM_STR);
    $stmt->bindValue(3, $duration, PDO::PARAM_INT);
    $stmt->bindValue(4, $end_time, PDO::PARAM_STR);

    if ($stmt->execute()) {
        print_r('<script>alert("The exam has started!")</script>');
        exit();
    } else {
        echo 'Error adding data: ' . implode(', ', $stmt->errorInfo());
    }
}

?>

<main id="main" class="main">
    <div class="container">
        <h2 class="mt-4">Create and Start the Exam!</h2>
        <form id="examForm" method="POST">
            <div id="questionContainer">
                <div class="form-group mt-4 question-block">
                    <label>Question 1</label>
                    <input type="text" class="form-control" name="question[]" placeholder="Enter the question" autocomplete="off">

                    <label class="mt-2">Answers</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[0][is_correct]" value="a">
                        <input type="text" class="form-control form-control-sm" name="answers[0][a]" placeholder="Answer A" autocomplete="off">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[0][is_correct]" value="b">
                        <input type="text" class="form-control form-control-sm" name="answers[0][b]" placeholder="Answer B" autocomplete="off">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[0][is_correct]" value="c">
                        <input type="text" class="form-control form-control-sm" name="answers[0][c]" placeholder="Answer C" autocomplete="off">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[0][is_correct]" value="d">
                        <input type="text" class="form-control form-control-sm" name="answers[0][d]" placeholder="Answer D" autocomplete="off">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-4" id="addQuestionBtn">+ Add Question</button>

            <div class="form-group mt-4">
                <label>Enter Duration (Minutes)</label>
                <select class="form-control" name="duration">
                    <?php for($i = 5; $i <= 90; $i += 5): ?>
                        <option value="<?= $i ?>"><?= $i ?> minutes</option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success mt-4">Start Exam</button>
        </form>
    </div>
</main>

<script>
    let questionCount = 1;

    document.getElementById('addQuestionBtn').addEventListener('click', function() {
        questionCount++;
        const questionBlock = document.createElement('div');
        questionBlock.classList.add('form-group', 'mt-4', 'question-block');
        questionBlock.innerHTML = `             
    <label>Question ${questionCount}</label>             
    <input type="text" class="form-control" name="question[]" placeholder="Enter the question" autocomplete="off">              
    <label class="mt-2">Answers</label>             
    <div class="form-check">                 
        <input class="form-check-input" type="radio" name="answers[${questionCount - 1}][is_correct]" value="a">                 
        <input type="text" class="form-control form-control-sm" name="answers[${questionCount - 1}][a]" placeholder="Answer A" autocomplete="off">             
    </div>             
    <div class="form-check">                 
        <input class="form-check-input" type="radio" name="answers[${questionCount - 1}][is_correct]" value="b">                 
        <input type="text" class="form-control form-control-sm" name="answers[${questionCount - 1}][b]" placeholder="Answer B" autocomplete="off">             
    </div>             
    <div class="form-check">                 
        <input class="form-check-input" type="radio" name="answers[${questionCount - 1}][is_correct]" value="c">                 
        <input type="text" class="form-control form-control-sm" name="answers[${questionCount - 1}][c]" placeholder="Answer C" autocomplete="off">             
    </div>             
    <div class="form-check">                 
        <input class="form-check-input" type="radio" name="answers[${questionCount - 1}][is_correct]" value="d">                 
        <input type="text" class="form-control form-control-sm" name="answers[${questionCount - 1}][d]" placeholder="Answer D" autocomplete="off">             
    </div>`;

        document.getElementById('questionContainer').appendChild(questionBlock);
    });
</script>
