<?php  
    require_once "php/database-handler.php";
  
    session_start();
    
    if(!isset($_SESSION['loggedin'])){
        header('Location: login.php');
        exit(0);
    }

    $con = Database::get_db_connection();

    // Obtain currently logged in admin information
    $su_access = $_SESSION['su_access'];
    $admin_email = $_SESSION['admin_email'];
    $admin_username = $_SESSION['admin_username'];

?>
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

        <title>TASK Admin Portal Home</title>

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

    <!-- Main home page -->
    <body style="background-color: white;" id="page-top">

        
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-md fixed-top bg-dark border-bottom p-3 border-body shadow">
            <div class="container-fluid">
                    <a class="navbar-brand text-white" href="index.php">
                        <img src="./assets/images/Task Logo Color JPG.png" alt="task_logo.png" height="25px" class="d-inline-block align-text-top me-3">
                        Learning Module Admin Portal
                    </a>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        <!--  Only give access to Admin management if given superuser access-->
                        <?php
                            if($su_access === 1)
                        {
                        ?>
                             <li class="nav-item">
                                <a class="nav-link text-light" href="admins-table.php">Admins</a>
                            </li>
                        <?php
                        }
                        ?>

                        <li class="nav-item">
                            <a class="nav-link text-light" href="users-table.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="lesson-modules-table.php">Modules</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="quizzes-table.php">Quizzes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="about.php">About Us</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class= "nav-link text-light"><i>Currently Logged in as: <?=$admin_email?></i></a>
                        </li>
                        <li class="nav-item5">
                            <a class="text-light btn btn-secondary ml-4" href="logout.php">Logout</a>
                        </li>
                    </ul>
            </div>
        </nav>
        <!-- End of Navigation Bar -->

         <!-- Page Wrapper Div -->
        <div id="wrapper">

           

            <!-- Call to Action page (Admin home) -->
            <div class="container justify-content-center centered " id="main-task-action-list">

                <div class ="row text-center mb-5">

                    <h1>Welcome back, <?=$admin_username?>!</h1>
                    <?php
                        if($su_access == 1)
                        {
                    ?>
                            <h4 style="color: green;">This account has Superuser access</h4>
                    <?php
                        }
                    ?>

                </div>

                <div class="row">

                    <!--  Only render this page, allow admin management if admin is a superuser -->
                    <?php
                        if($su_access === 1)
                        {   
                    ?>
                            <div class="col-md">
                                <div class="card shadow rounded" style="max-width:20rem; min-height:30rem;">
                                    <img src="./assets/images/task_staff_admins.jpg" class="card-img-top" style="max-width:20rem; padding:0px;" alt="admin_management_card">
                                    <div class="card-body">
                                        <h5 class="card-title">Admins</h5>
                                        <p class="card-text overflow-auto">Click here to view the list of current Admins for the TASK Learning Module.</p>
                                    </div>
                                    <div class="p-4">
                                        <a href="admins-table.php" class="btn btn-primary">Enter</a>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    ?>
                    <div class="col-md">
                        <div class="card shadow rounded" style="max-width:20rem; min-height:30rem;">
                            <img src="./assets/images/task-meal.jpg" class="card-img-top" style="max-width:20rem;" alt="user_management_card">
                            <div class="card-body">
                                <h5 class="card-title">Current Users</h5>
                                <p class="card-text overflow-auto">Click here to view the list of users of who are currently using the web application.</p>
                            </div>
                            <div class="p-4">
                                <a href="users-table.php" class="btn btn-primary">Enter</a>
                            </div>  
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="card shadow rounded" style="max-width:20rem; min-height:30rem;">
                            <img src="./assets/images/learning_module_image.jpg" class="card-img-top" style="max-width:20rem;" alt="lesson_module_management_card">
                            <div class="card-body">
                                <h5 class="card-title">Lesson Modules</h5>
                                <p class="card-text overflow-auto">Click here to manage learning module information. </p>
                            </div>
                            <div class="p-4">
                                <a href="lesson-modules-table.php" class="btn btn-primary">Enter</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="card shadow rounded" style="max-width:20rem; min-height:30rem;">
                            <img src="./assets/images/quiz_module_image.jpg" class="card-img-top" style="max-width:20rem;" alt="quiz_management_card">
                            <div class="card-body">
                                <h5 class="card-title">Quizzes</h5>
                                <p class="card-text overflow-auto">Click here for quiz-related tasks.</p>
                            </div>
                            <div class="p-4">
                                <a href="quizzes-table.php" class="btn btn-primary">Enter</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md">
                        <div class="card shadow rounded" style="max-width:20rem; min-height:30rem;">
                            <img src="./assets/images/task_building_image.jpg" class="card-img-top" style="max-width:20rem;" alt="quiz_management_card">
                            <div class="card-body">
                                <h5 class="card-title">About Us</h5>
                                <p class="card-text overflow-auto">Click here for information about TASK and Bonner; contributors of the project</p>
                            </div>
                            <div class="p-4">
                                <a href="about.php" class="btn btn-primary">Enter</a>
                            </div> 
                        </div>
                    </div>
                    
                    
                </div>

            </div>
            <!-- End of Call to Action page (Admin home) -->

        </div>
        <!-- End of Page Wrapper Div -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html