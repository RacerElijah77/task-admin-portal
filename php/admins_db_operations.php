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

    // Check if the admin email already exists in the database before you add the entry (no duplicate records)
    // The following POST request will generate a popup message in the frontend form to tell if fields are already taken.
    if(isset($_POST['duplicate_email_check'])){
        
        $adminEmailCheck = $_POST['email_check'];
        $email_query = $con->prepare("SELECT * FROM admins WHERE admin_email=?");
        $email_query->bind_param("s", $adminEmailCheck);
        $email_query->execute();

        $email_query->store_result();

        if($email_query->num_rows > 0){
            echo "<small style='color:red;'>Email Already Exists in portal. Please enter another email.</small>";
        }
        else{
            if($adminEmailCheck === "")
                echo "";
            elseif(!str_contains($adminEmailCheck, '@'))
                echo "<small style='color:red;'>Email must contain '@' </p>";
            else
                echo "<small style='color:green;'>Email is available.</small>";
        }
    }

    // Check if the admin username already exists in the database before you add the entry (no duplicate records)
    // The following POST request will generate a popup message in the frontend form to tell if fields are already taken.
    if(isset($_POST['duplicate_username_check'])){
        
        $adminUsernameCheck = $_POST['username_check'];
        $username_query = $con->prepare("SELECT * FROM admins WHERE admin_username=?");
        $username_query->bind_param("s", $adminUsernameCheck);
        $username_query->execute();

        $username_query->store_result();

        if($username_query->num_rows > 0){
            echo "<small style='color:red;'>Username Already Exists in portal. Please enter another username.</small>";
        }
        else{
            if($adminUsernameCheck === "")
                echo "";
            else
                echo "<small style='color:green;'>Username is available.</small>";
        }
    }

    // Check whether both passwords match before form submission
    // The following POST request will generate a popup message in the frontend form to tell if fields are already taken.
    if(isset($_POST['mismatch_password_check'])){
        
        $rawPassword = $_POST['raw_password'];
        $adminConfirmPassword = $_POST['re_password'];

        if($rawPassword !== $adminConfirmPassword){
            echo "<small style='color:red;'>Passwords do not match!</small>";
        }
        else{
            if($adminConfirmPassword === "")
                echo "";
            else
                echo "<small style='color:green;'>Passwords match!</small>";
        }
    }

    // Adding Admin Entry
    if(isset($_POST['add_admin'])){  

        $adminEmail = $_POST['admin_email'];
        $adminUsername = $_POST['admin_username'];
        $adminPassword = $_POST['admin_password'];
        $adminConfirmPassword = $_POST['re-admin_password'];
        $suAccess = 0;

        // Determine if account is going to be a superuser account
        if($_POST['is_superuser'] === 'on'){
            $suAccess = 1;
        }

        $dateCreated = date('Y-m-d');
        $lastLogin = null;

        // Check if email exists in database, no duplicate entries!
        $email_query = $con->prepare("SELECT * FROM admins WHERE admin_email=?");
        $email_query->bind_param("s", $adminEmail);
        $email_query->execute();

        $email_query->store_result();
        
        // Check if username exists in databasem no duplicate entries!
        $username_query = $con->prepare("SELECT * FROM admins WHERE admin_username=?");
        $username_query->bind_param("s", $adminUsername);
        $username_query->execute();

        $username_query->store_result();


        // Disband the query if new passwords do not match when adding an admin
        if($adminPassword !== $adminConfirmPassword){
            $_SESSION['message'] = "Admin cannot be added; passwords do not match!";
            header('Location: ../admins-table.php');
            exit(0);
        }
        elseif ($email_query->num_rows > 0 or $username_query->num_rows > 0) {
            $_SESSION['message'] = "Admin cannot be added; Email AND/OR Username is already taken!";
            header('Location: ../admins-table.php');
            exit(0);
        }

        // Hash password before adding it into the table, uses standard bcrypt hashing algorithm
        $adminPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

        $sql_stmt = $con->prepare("INSERT INTO admins VALUES ('0', ?, ?, ?, ?, ?, ?)");
        $sql_stmt->bind_param("sssiss", $adminEmail, $adminPassword, $adminUsername, $suAccess, $dateCreated, $lastLogin);
        $rs = $sql_stmt->execute();

        if($rs){
            $_SESSION['message'] = "Admin Added Successfully!";
            header('Location: ../admins-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "Admin Not Added! Error Encountered.";
            header('Location: ../admins-table.php');
            exit(0);
        }

    }

    // Editing Admin Entry (should be presented with password for selected ID first)
    if(isset($_POST['edit_admin'])){

        $adminId =  $_POST['admin_id'];
        $adminEmail = $_POST['admin_email'];
        $adminUsername = $_POST['admin_username'];
        $suAccess = 0;

        // Determine if account is going to be a superuser account
        if($_POST['is_superuser'] === 'on'){
            $suAccess = 1;
        }

        $sql_stmt = $con->prepare("UPDATE admins SET admin_email=?, admin_username=?, su_access=? WHERE admin_id=?");
        $sql_stmt->bind_param("ssii", $adminEmail, $adminUsername, $suAccess, $adminId);
        $rs = $sql_stmt->execute();

        if($rs){
            $_SESSION['message'] = "Admin Updated Successfully!";
            // $_SESSION['admin_email'] = $adminEmail;
            header('Location: ../admins-table.php');
            exit(0);
        }
        else{
            $_SESSION['message'] = "Admin Not Updated! Error Encountered.";
            header('Location: ../admins-table.php');
            exit(0);
        }
    }

    // Delete Admin Entry
    if(isset($_POST['delete_admin'])){

        // Obtain confirmation text and ID if they really want to delete the module
        $confirmation_password = $_POST['delete_admin'];
        $adminId = $_POST['admin_id'];

        $session_adminId = $_POST['session_admin_id'];

        // Superuser account is bonnertaskpass@gmail.com, and should have and admin_id = 1
        $sql_stmt = $con->prepare("SELECT admin_id, admin_password FROM admins WHERE admin_email = 'bonnertaskpass2024@gmail.com'");
        $rs = $sql_stmt->execute();

        $sql_stmt->store_result();

        // Check if the superuser account does exist in the database
        if($sql_stmt->num_rows > 0){
            $sql_stmt->bind_result($admin_su_id, $admin_su_password);
            $sql_stmt->fetch();
           
        }
        else{
            $_SESSION['message'] = "Could not find superuser account!";
            header('Location: ../admins-table.php');
            exit(0);
        }

        // If the password of the superuser admin is not equal to provided password; do not delete entry.
        if(!password_verify($confirmation_password, $admin_su_password)){
            $_SESSION['message'] = "Cannot delete entry; superuser password is incorrect.";
            header('Location: ../admins-table.php');
            exit(0);
        }
        else{

            //Check if the current session admin id is the equal to the admin we want to delete
            //If both IDs are equal, the candidate account will need to be logged out before deleting from table
            $id_flag = 0;
            if($session_adminId === $adminId){
                $id_flag = 1;
            }
            else{
                echo "Session ID and the other id are not the same";
            }

            $sql_stmt = $con->prepare("DELETE FROM admins WHERE admin_id=?");
            $sql_stmt->bind_param("i", $adminId);
            $rs = $sql_stmt->execute();
            
            if($rs){
                $_SESSION['message'] = "Admin Deleted Successfully!";

                // Redirect user back to login page if they delete themselves (session)
                if($id_flag === 1){
                    header('Location: ../login.php');
                }
                else{
                    header('Location: ../admins-table.php');
                }
            }
            else{
                $_SESSION['message'] = "Admin Not Deleted! Error Encountered.";
                header('Location: ../admins-table.php');
                exit(0);
            }
        }

    }

?>