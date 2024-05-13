<?php
    require_once "database-handler.php";
    session_start();
    // Call this function from the database-handler.php script import
    $con = Database::get_db_connection();

    // // to ensure that the connection is made
    if (!$con)
    {
        die("Connection failed!" . mysqli_connect_error());
    }

    
    
    // Adding Quiz Question entry
    // NOTE: Auto increment the "Number of questions" field in the Quiz table when entry is added; decrement when question is removed
    if(isset($_POST['add_quiz_question'])){  
    
        // Obtain Quiz ID  for SQL add quiz question query (explode() function)
        $selectQuiz = $_POST['module-choice'];
        $splitModTemp = explode("-", $selectQuiz);

        $selectedQuizID = $splitModTemp[0];
        $selectedModuleName = $splitModTemp[1];

        $quizQuestionText = $_POST['quiz_question_text'];
        $questionNo = $_POST['question_no'];
        $questionPoints = $_POST['question_points'];

        echo $selectedQuizID;
        echo $quizQuestionText;
        echo $questionNo;
        echo $questionPoints;

        $sql_stmt = $con->prepare("INSERT INTO lessonQuizQuestions VALUES ('0', ?, ?, ?, ?, ?)");
        $sql_stmt->bind_param("issii", $selectedQuizID, $selectedModuleName, $quizQuestionText, $questionNo, $questionPoints);
        $rs = $sql_stmt->execute();

        // Make sure to update the "num_questions column" for the selected quiz when the quiz question is added
        $update_question_count = $con->prepare("UPDATE lessonQuizzes SET num_questions = num_questions + 1 WHERE quiz_id=?");
        $update_question_count->bind_param("i", $selectedQuizID);
        $rs2 = $update_question_count->execute();

        // Make sure to update the "max_possible_score column" for the selected quiz when quiz question is added
        $update_max_score = $con->prepare("UPDATE lessonQuizzes SET max_possible_score = max_possible_score + $questionPoints WHERE quiz_id=?");
        $update_max_score->bind_param("i", $selectedQuizID);
        $rs3 = $update_max_score->execute();
    
        if($rs and $rs2 and $rs3){
            $_SESSION['message'] = "Quiz Question Added Successfully!";
            header('Location: ../quiz-question-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "Quiz Question Not Added! Error Encountered.";
            header('Location: ../quiz-question-table.php');
            exit(0);
        }

    }

    // Editing Quiz Question Entry
    if(isset($_POST['edit_quiz_question'])){
   
        $quizQuestionID =  $_POST['quiz_question_id'];
        $selectedQuizID = $_POST['quiz_id'];
        $quizQuestionText = $_POST['quiz_question_text'];
        $questionNo = $_POST['question_no'];
        $questionPoints = $_POST['question_points'];

        $savedQuestionPoints = $_POST['old_question_points'];

        echo $savedQuestionPoints;

        $sql_stmt = $con->prepare("UPDATE lessonQuizQuestions SET quiz_question_text=?, question_no=?, question_points=? WHERE quiz_question_id=?");
        $sql_stmt->bind_param("siii", $quizQuestionText, $questionNo, $questionPoints, $quizQuestionID);
        $rs = $sql_stmt->execute();

        // Make sure to update the "max_possible_score column" for the selected quiz when quiz question is edited
        $update_max_score = $con->prepare("UPDATE lessonQuizzes SET max_possible_score = max_possible_score - $savedQuestionPoints + $questionPoints WHERE quiz_id=?");
        $update_max_score->bind_param("i", $selectedQuizID);
        $rs2 = $update_max_score->execute();

        if($rs and $rs2){
            $_SESSION['message'] = "Quiz Question Updated Successfully!";
            header('Location: ../quiz-question-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "Quiz Question Not Updated! Error Encountered.";
            header('Location: ../quiz-question-table.php');
            exit(0);
        }
    }

    // Delete Chosen Quiz Question Entry
    if(isset($_POST['delete_quiz_question'])){

        // Obtain confirmation text and ID if they really want to delete the module
        $confirmationText = $_POST['delete_quiz_question'];
        $bothQuizIDs = $_POST['both_quiz_ids'];

        // Need to obtain both the quiz question ID and quiz ID for the question count query
        $splitModTemp = explode("-", $bothQuizIDs);

        $quizQuestionID = $splitModTemp[0];
        $quizID = $splitModTemp[1];
        $questionPoints = $splitModTemp[2];

        // If the strings are not equal; do not delete entry.
        if(strcmp($confirmationText,"DELETE") !== 0 ){
            $_SESSION['message'] = "Cannot delete entry; admin did not type DELETE.";
            header('Location: ../quiz-question-table.php');
        }
        else{

            $sql_stmt = $con->prepare("DELETE FROM lessonQuizQuestions WHERE quiz_question_id=?");
            $sql_stmt->bind_param("i", $quizQuestionID);
            $rs = $sql_stmt->execute();

            // Make sure to update the "num_questions column" for the selected quiz when the quiz question is deleted
            $update_question_count = $con->prepare("UPDATE lessonQuizzes SET num_questions = num_questions - 1 WHERE quiz_id=?");
            $update_question_count->bind_param("i", $quizID);
            $rs2 = $update_question_count->execute();

            // Make sure to update the "max_possible_score column" for the selected quiz when quiz question is deleted
            $update_max_score = $con->prepare("UPDATE lessonQuizzes SET max_possible_score = max_possible_score - $questionPoints WHERE quiz_id=?");
            $update_max_score->bind_param("i", $quizID);
            $rs3 = $update_max_score->execute();
    
            if($rs and $rs2 and $rs3){
                $_SESSION['message'] = "Quiz Question Deleted Successfully!";
                header('Location: ../quiz-question-table.php');
                exit(0);
            }
            else{
                $_SESSION['message'] = "Quiz Question Not Deleted! Error Encountered.";
                header('Location: ../quiz-question-table.php');
                exit(0);
            }
            
        }

    }

    // Redirect back to table list page after table operation is complete
    // header('Location: ../quiz-question-table.php');
    // exit(0);

?>