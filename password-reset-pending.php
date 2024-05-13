<?php include('php/reset_password_logic.php'); ?>

<!DOCTYPE html>
<html lang="en">

  <head>
          <meta charset="UTF-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          
          <!-- Custom fonts for this template-->
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

          <!-- Google Fonts -->
          <link rel="preconnect" href="https://fonts.googleapis.com">
          <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
          <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
          <link href="css/font-styles.css" rel="stylesheet">

          <title>TASK Password Reset</title>

          <!-- Temporary styling used for the homepage menu -->
          <style>
              .centered {
                  position: absolute;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
              }
          </style>

      </head>

  <body role="region" aria-label="Body">

    <div class="container justify-content-center centered">
      
      <div class="row justify-content-center">

        <div class="col-md-9">

          <div class="card o-hidden border-0 shadow-lg my-3">
            
            <div class="card-body p-0 justify-content-center" role="region" aria-label="Login Card">
              <!-- Nested Row within Card Body -->
              <div class="row justify-content-center">
                <div class="col-lg-10 justify-content-center">
                  <div class="p-5">
                    <div class="text-center">
                      <h1 class="h4 text-gray-900 mb-4">Check your Email!</h1>
                    </div>

                    <form action="php/reset_admin_password_logic.php" method = "POST" >

                        <div class="form-group mb-4 text-center">
                            <p> We sent an email to <b><?=$_GET['admin_email']?></b> to help you recover your account.
                            <p> Please login to your email account and click on the link we sent to reset your password. </p>
                        </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
  
</html>
