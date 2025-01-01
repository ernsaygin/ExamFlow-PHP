# ExamFlow-PHP

**ExamFlow-PHP** is a simple PHP-based exam management system that allows users to create exams, participate in them, and evaluate results. The system supports essential features such as exam durations, question types, and exam retake restrictions. It can be used for both educational purposes and fun quizzes.

## Features

- **Create Exams:** Users can create exams with various question types, including multiple choice, true/false, and fill-in-the-blank questions.
- **Exam Duration:** A time limit can be set for each exam, and the exam automatically ends when the time runs out.
- **User Participation:** Registered users can participate in exams. However, once a user completes an exam, they cannot retake the same exam immediately.
- **Results Evaluation (No Display):** Users cannot view their exam results. The results are recorded after completing the exam, but no visual feedback is provided.
- **Admin Panel (For Example Purposes):** The admin can manage exams and users. The admin panel is included as an example to track and manage results, though it provides a simple layout without advanced design features.

## Use Cases

**ExamFlow-PHP** can be used for various scenarios, such as educational institutions, personal exam applications, quizzes, and tests. It is suitable for students, instructors, or anyone interested in creating fun or educational exams. The admin panel and results evaluation features are added for demonstration purposes and can be developed further according to user needs.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/ExamFlow-PHP.git

2. Set up your database:
   - Import the `module.sql` file into your MySQL database. You can do this by running the appropriate SQL command in your MySQL client.

3. Configure the database connection in `database.php`:
   - Open the `database.php` file and set the correct database credentials (username, password, host, and database name).

4. Once the database is set up and the configuration is complete, you can start using the application through your web server.
