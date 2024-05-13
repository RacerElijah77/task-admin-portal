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

    
    
    // Adding Quiz entry
    if(isset($_POST['add_quiz'])){  
    
        // Split the module ID and module Name into seperate variables for SQL add quiz query (explode() function)
        $selectModule = $_POST['module-choice'];
        $splitModTemp = explode("-", $selectModule);


        $selectedModuleID = $splitModTemp[0];
        $selectedModuleName = $splitModTemp[1];
        $quizDescription = $_POST['quiz_description'];
        $numQuestions = 0;
        $elapsedTime = $_POST['elapsed_time'];
        $maxPossibleScore = 0;

        $dateCreated = date('Y-m-d');

        echo $selectedModuleID;
        echo $selectedModuleName;
        echo $quizDescription;
        echo $numQuestions;
        echo $elapsedTime;
        echo $maxPossibleScore;
        echo $dateCreated;


        $sql_stmt = $con->prepare("INSERT INTO lessonQuizzes VALUES ('0', ?, ?, ?, ?, ?, ?, ?)");
        $sql_stmt->bind_param("issiiis", $selectedModuleID, $selectedModuleName, $quizDescription, $numQuestions, $elapsedTime, $maxPossibleScore, $dateCreated);
        $rs = $sql_stmt->execute();
    
        if($rs){
            $_SESSION['message'] = "Quiz Added Successfully!";
            header('Location: ../quizzes-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "Quiz Not Added! Error Encountered.";
            header('Location: ../quizzes-table.php');
            exit(0);
        }

    }

    // Editing Quiz Entry
    if(isset($_POST['edit_quiz'])){
   
        $quizId =  $_POST['quiz_id'];
        $quizDescription = $_POST['quiz_description'];
        $numQuestions = $_POST['num_questions'];
        $elapsedTime = $_POST['elapsed_time'];
        $maxPossibleScore = $_POST['max_possible_score'];


        $sql_stmt = $con->prepare("UPDATE lessonQuizzes SET quiz_description=?, num_questions=?, elapsed_time=?, max_possible_score=? WHERE quiz_id=?");
        $sql_stmt->bind_param("siiii", $quizDescription, $numQuestions, $elapsedTime, $maxPossibleScore, $quizId);
        $rs = $sql_stmt->execute();

        if($rs){
            $_SESSION['message'] = "Quiz Updated Successfully!";
            header('Location: ../quizzes-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "Quiz Not Updated! Error Encountered.";
            header('Location: ../quizzes-table.php');
            exit(0);
        }
    }

    // Delete Chosen Quiz Entry
    if(isset($_POST['delete_quiz'])){

        // Obtain confirmation text and ID if they really want to delete the module
        $confirmationText = $_POST['delete_quiz'];
        $quizId = $_POST['quiz_id'];

        // If the strings are not equal; do not delete entry.
        if(strcmp($confirmationText,"DELETE") !== 0 ){
            $_SESSION['message'] = "Cannot delete entry; admin did not type DELETE.";
            header('Location: ../quizzes-table.php');
        }
        else{

            $sql_stmt = $con->prepare("DELETE FROM lessonQuizzes WHERE quiz_id=?");
            $sql_stmt->bind_param("i", $quizId);
            $rs = $sql_stmt->execute();
    
            if($rs){
                $_SESSION['message'] = "Quiz Deleted Successfully!";
                header('Location: ../quizzes-table.php');
                exit(0);
            }
            else{
                $_SESSION['message'] = "Quiz Not Deleted! Error Encountered.";
                header('Location: ../quizzes-table.php');
                exit(0);
            }
            
        }

    }

    // Redirect back to table list page after table operation is complete
    header('Location: ../quizzes-table.php');
    exit(0);

?>