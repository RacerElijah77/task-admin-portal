<!-- Finish editing this form -->
<?php
    require_once "../php/database-handler.php";

    session_start();
    if(!isset($_SESSION['loggedin'])){
        header('Location: ../login.php');
        exit(0);
    }
    
    $con = Database::get_db_connection();

    // to ensure that the connection is made
    if (!$con)
    {
        die("Connection failed!" . mysqli_connect_error());

    }
    // Obtain currently logged in admin information
    $su_access = $_SESSION['su_access'];
    $admin_email = $_SESSION['admin_email'];
    $admin_username = $_SESSION['admin_username'];
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- Custom fonts for this template-->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        <link href="../css/font-styles.css" rel="stylesheet">
        
        <title>TASK Admins - Edit</title>

        <!-- Basic CSS to center page elements -->
        <style>
            .centered {
                position: fixed;
                top: 50%;
                left: 50%;
                /* bring your own prefixes */
                transform: translate(-50%, -50%);
            }
        </style>

    </head>

    <body style="background-color: white;" id="page-top">

        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-md fixed-top bg-dark border-bottom p-3 border-body shadow">
            <div class="container-fluid">
                    <a class="navbar-brand text-white" href="../index.php">
                        <img src="../assets/images/Task Logo Color JPG.png" alt="task_logo.png" height="25px" class="d-inline-block align-text-top me-3">
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
                            <a class="nav-link text-light" href="../users-table.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="../lesson-modules-table.php">Modules</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="../quizzes-table.php">Quizzes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="../about.php">About Us</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class= "nav-link text-light"><i>Currently Logged in as: <?=htmlspecialchars($_SESSION['admin_email'], ENT_QUOTES)?></i></a>
                        </li>
                        <li class="nav-item5">
                            <a class="text-light btn btn-secondary ml-4" href="../logout.php">Logout</a>
                        </li>
                    </ul>
            </div>
        </nav>
        <!-- End of Navigation Bar -->

        <!-- Page Wrapper Div -->
        <div id="wrapper">

            <?php include('../php/message.php'); ?>

            <!-- Edit Entry Form HTML -->
            <div class="container justify-content-center centered w-50">

                <h2>TASK Admin Edit </h2>

                <?php
                if(isset($_GET['id'])) {
        
                    // Setup retireval Query for form edit
                    $admin_id = mysqli_real_escape_string($con, $_GET['id']);
                    $query = "SELECT * FROM admins WHERE admin_id='$admin_id'";
                    $query_run = mysqli_query($con, $query);

                    if(mysqli_num_rows($query_run) > 0){
                        $selectedAdmin = mysqli_fetch_array($query_run);
                        
                        ?>
                            <!-- User should enter password based on the admin ID first before editing  -->
                            <form action="../php/admins_db_operations.php" method="POST">
                                <input type="hidden" name="admin_id" value="<?= $selectedAdmin['admin_id']; ?>">
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Admin Email</label>
                                    <input type="email" name="admin_email" id="admin_email" class="form-control" value="<?=$selectedAdmin['admin_email']?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_username" class="form-label">Admin Username</label>
                                    <textarea class="form-control" name="admin_username" id="admin_username" required><?=$selectedAdmin['admin_username']?></textarea>
                                </div>

                                <div class="form-check mb-3">
                                    <?php
                                        if($selectedAdmin['su_access'] == 1)
                                        {
                                    ?>
                                            <input class="form-check-input" type="checkbox" name="is_superuser" id="is_superuser" checked>
                                    <?php
                                        }
                                        else
                                        {
                                    ?>
                                            <input class="form-check-input" type="checkbox" name="is_superuser" id="is_superuser">
                                    <?php
                                        }
                                    ?>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Give Superuser access to this account?
                                    </label>
                                </div>

                                <!-- Password change should be done from the login page-->

                                <div class="modal-footer">
                                    <a href="../admins-table.php" class="btn btn-secondary" role="button">Close</a>
                                    <!-- Execute SQL query when button is pressed -->
                                    <button type="submit" name = "edit_admin" class="btn btn-primary">Save Changes</button>

                                </div>
                            </form>
                        <?php      
                    }
                    else{
                        // Throw error here, no entry found
                        echo "<h1>No Entry Found</h1>";
                    }
                }
                ?>

            </div>

        </div>
        <!-- End of Page Wrapper Div -->
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>