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

    // Check if the data from login form was submitted, isset() checks if the data exists
    if(!isset($_POST['admin_email'], $_POST['admin_password'])){
        exit('Please fill in both the email and password fields!');
    }
    else{
        // debug output, need to comment out!
        // echo $_POST['admin_email'];
        // echo $_POST['admin_password'];
    }

    // Prepare SQL statement to prevent SQL injection
    $sql_stmt = $con->prepare("SELECT admin_id, admin_password, su_access, admin_username FROM admins WHERE admin_email = ?");
    $sql_stmt->bind_param("s", $_POST['admin_email']);
    $rs = $sql_stmt->execute();

    // Store the result to check if the account already exists in the database.
    $sql_stmt->store_result();

    
    // Acccount does exist in the  if true, need to verify the password
    if($sql_stmt->num_rows > 0){

        // If username exists, bind the results to the $admin_id and $admin_password variables.
        $sql_stmt->bind_result($admin_id, $admin_password, $su_access, $admin_username);
        $sql_stmt->fetch();

        //Call the password_verify function built by PHP, which supports SHA_512 password hashes through the crypt() function call
        //This function only works with passwords created using the password_hash() function 
        // (when adding new admin entries, call this function)
        if(password_verify($_POST['admin_password'], $admin_password)){
            
            // Create a new session, so we know that the user is logged in, (cookies)
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['admin_email'] = $_POST['admin_email'];
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['su_access'] = $su_access;
            $_SESSION['admin_username'] = $admin_username;

            // echo $_POST['su_access'];

            $lastLogin = date('Y-m-d');

            // Automatically update "last-login" date when admin signs in
            $sql_stmt = $con->prepare("UPDATE admins SET last_login=? WHERE admin_id=?");
            $sql_stmt->bind_param("si", $lastLogin, $_SESSION['admin_id']);
            $rs = $sql_stmt->execute();
        }
        else{
            //use message.php file to display modal screen
            echo 'Incorrect username and/or password!';
        }
    }
    else{
         //use message.php file to display modal screen
        echo 'Incorrect username and/or password!';
    }

    $sql_stmt->close();

    header('Location: ../index.php');
    exit(0);

?>