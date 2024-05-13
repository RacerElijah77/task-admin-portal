<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use League\OAuth2\Client\Provider\Google;
    use PHPMailer\PHPMailer\SMTP;

    require_once "database-handler.php";
    session_start();
    // Call this function from the database-handler.php script import
    $con = Database::get_db_connection();

    // // to ensure that the connection is made
    if (!$con)
    {
        die("Connection failed!" . mysqli_connect_error());
    }

    // Create an array of potential errors for various password reset cases
    $errors = [];


    
    // Accept the email of use whose password is to be reset
    // Goal is to send an email to the user to reset their password
    if(isset($_POST['reset-password'])){


        $admin_email = mysqli_real_escape_string($con, $_POST['admin_email']);

        $sql_stmt = $con->prepare("SELECT admin_email FROM admins WHERE admin_email = ?");
        $sql_stmt->bind_param("s", $_POST['admin_email']);
        $rs = $sql_stmt->execute();

        // Store the result to check if the account already exists in the database.
        $demo = $sql_stmt->store_result();

        // Check if the associated email address is in the database
        if($sql_stmt->num_rows <= 0){
            array_push($errors, "Sorry, no admin exists on our system with the specified email. Please enter a valid email address.");
            header('Location: ../forgot-password.php');
            exit(0);
        }
        
        // Send mail if email exists
        else{

            $output = '';

            // Generate time expiration date
            $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
            $expDate = date("Y-m-d H:i:s", $expFormat);

            // Generate token (with time expiration) for password result; append this token to the URL of the password reset link:
            $token = md5(time());
            $addToken = substr(md5( uniqid(rand() ,1)), 3, 10);
            $token = $token . $addToken;

            // Make sure to add password request entry + token for tracking when the admin clicks on the link for password reset.
            $sql_stmt = $con->prepare("INSERT INTO password_reset_temp VALUES (?, ?, ?)");
            $sql_stmt->bind_param("sss", $admin_email, $token, $expDate);
            $rs = $sql_stmt->execute();

            // Debugging purposes
            // if($rs){
            //     echo "Query Executed" . $token . $admin_email;
            // }
            // else{
            //     echo "Query Failed"
            // }

            $output.='<p>Hello ' . $admin_email . 'You are recieving this email because you have decided to reset your password.</p>';

            // Remember to change the domain name to production server after migration occurs.
            $output.='<p><a href="https://task.hpc.tcnj.edu/task-admin-portal/new-password.php?token='.$token . '&email=' . $email  . '&action=reset" target="_blank">Reset Password</a></p>';
            
            $body = $output;
            $subject = "Reset your password for TASK/Bonner Admin Portal";
            $send_to = $admin_email;


            // Google OAuth2 SMTP Configuration Settings before sending email.
            $googleEmail = 'bonnertaskpass2024@gmail.com';
            $clientId = '538070356483-0ds16ve5fote3ctbj4c54fpsiubs4fa2.apps.googleusercontent.com';
            $clientSecret = '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';
            $refreshToken = '1//05SmvE9fFDvxpCgYIARAAGAUSNwF-L9Irj4kowelBKa8V2dNhYfCmKIxSSRwivKvelyaqLwaRZQf96K4src-g_9b5OkUKLxhtCj4';

            // Bug,still need to fix
            require("../vendor/autoload.php");
            $mail = new PHPMailer(TRUE);
            $mail->Mailer = "smtp";
            $mail->setFrom($googleEmail);
            $mail->addAddress($send_to);

            $mail->Username = $googleEmail;
            $mail->Password = 'bsff zlvi szzm sftm';

            $mail->Subject = $subject;
            $mail->IsSMTP();
            $mail->Port = 587;
            $mail->SMTPAuth = TRUE;
            $mail->SMTPSecure = 'tls';
            $mail->IsHTML(TRUE); 
            $mail->Body = $body;
           
            // $mail->Username = $googleEmail;
            // $mail->Password = '';

            // Google's SMTP 
            $mail->Host = 'smtp.gmail.com';

            // Set AuthType to XOAUTH2. 
            $mail->AuthType = 'XOAUTH2';


            // Create a new OAuth2 provider instance. 
            $provider = new Google([
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
            ]);

            // // BUG: Pass the OAuth provider instance to PHPMailer. 
            // $mail->setOAuth(
            //     new OAuth(
            //         [
            //             'provider' => $provider,
            //             'clientId' => $clientId,
            //             'clientSecret' => $clientSecret,
            //             'refreshToken' => $refreshToken,
            //             'userName' => $googleEmail,
            //         ]
            //     )
            // );

            $mail->SMTPDebug = 2;


            if(!$mail->send()){
                echo "Mailer Error" . $mail->ErrorInfo;
            }
            else{
                echo "Email has been sent!";
            }

            // $mail->smtpClose();

            $mail->Send();

        }

        $sql_stmt->close();
        
    }

    // Logic for entering a new password (REMEMBER TO HASH THE NEW PASSWORD!!!)
    if(isset($_POST['new-password'])){
       // echo "<h1>Hello Dr Knox!!</h1>";
    }

?>