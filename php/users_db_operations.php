<!-- Replace with admin database columns later -->
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

    // Adding User Entry
    if(isset($_POST['add_user'])){  

        $userEmail = $_POST['user_email'];
        $userFirstName = $_POST['user_first_name'];
        $userLastName = $_POST['user_last_name'];
        $userPassword = $_POST['user_password'];
        $userConfirmPassword = $_POST['re-user_password'];

        $dateCreated = date('Y-m-d');
        $lastLogin = null;
        $numberCompletedModules = 0;

        // Disband the query if new passwords do not match when adding an admin
        if($userPassword !== $userConfirmPassword){
            $_SESSION['message'] = "User cannot be added; passwords do not match!";
            header('Location: ../users-table.php');
            exit(0);
        }

        // Hash password before adding it into the table, uses standard bcrypt hashing algorithm
        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);

        $sql_stmt = $con->prepare("INSERT INTO users VALUES ('0', ?, ?, ?, ?, ?, ?, ?)");
        $sql_stmt->bind_param("ssssiss", $userEmail, $userFirstName, $userLastName, $dateCreated, $numberCompletedModules, $userPassword, $lastLogin);
        $rs = $sql_stmt->execute();

        if($rs){
            $_SESSION['message'] = "User Added Successfully!";
            header('Location: ../users-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "User Not Added! Error Encountered.";
            echo "Hello KSKSKS";
            header('Location: ../users-table.php');
            exit(0);
        }

    }

    // Editing User Entry (should be presented with password for selected ID first)
    if(isset($_POST['edit_user'])){

        $userId =  $_POST['user_id'];
        $userEmail = $_POST['user_email'];
        $userFirstName = $_POST['user_first_name'];
        $userLastName = $_POST['user_last_name'];

        $sql_stmt = $con->prepare("UPDATE users SET user_email=?, user_first_name=?, user_last_name=? WHERE user_id=?");
        $sql_stmt->bind_param("sssi", $userEmail, $userFirstName, $userLastName, $userId);
        $rs = $sql_stmt->execute();

        if($rs){
            $_SESSION['message'] = "User Updated Successfully!";
            header('Location: ../users-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "User Not Updated! Error Encountered.";
            header('Location: ../users-table.php');
            exit(0);
        }
    }

    // Delete User Entry
    if(isset($_POST['delete_user'])){

        // Obtain confirmation text and ID if they really want to delete the module
        $confirmationText = $_POST['delete_user'];
        $userId = $_POST['user_id'];

        // If the strings are not equal; do not delete entry.
        if(strcmp($confirmationText,"DELETE") !== 0 ){
            $_SESSION['message'] = "Cannot delete entry; admin did not type DELETE.";
            header('Location: ../users-table.php');
        }
        else{

            $sql_stmt = $con->prepare("DELETE FROM users WHERE user_id=?");
            $sql_stmt->bind_param("i", $userId);
            $rs = $sql_stmt->execute();
    
            if($rs){
                $_SESSION['message'] = "User Deleted Successfully!";
                header('Location: ../users-table.php');
                exit(0);
            }
            else{
                $_SESSION['message'] = "User Not Deleted! Error Encountered.";
                header('Location: ../users-table.php');
                exit(0);
            }
            
        }

    }

?>