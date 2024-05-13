<?php
    require_once "php/database-handler.php";
  
    // Make sure to establish database connection first!
    session_start();
    if(!isset($_SESSION['loggedin'])){
        header('Location: login.php');
        exit(0);
    }

    // Obtain currently logged in admin information
    $su_access = $_SESSION['su_access'];
    $admin_email = $_SESSION['admin_email'];
    $admin_username = $_SESSION['admin_username'];

    // Redirect back to homepage if admin is not superuser (do not force naviagate)
    if($su_access != 1){
        header('Location: index.php');
        exit(0);
    }

    $con = Database::get_db_connection();
    
    // SQL query to view all entries in selected table;
    $query_view_table = "SELECT * FROM admins";
    $query_run_view_table = mysqli_query($con, $query_view_table);

    // Obtain superuser information for front-end
    $obtain_su_account = "SELECT admin_id, admin_email, admin_username FROM admins WHERE admin_id=1";
    $query_run_obtain_su = mysqli_query($con, $obtain_su_account);
    $su_row = mysqli_fetch_assoc($query_run_obtain_su);

?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- Custom fonts for this template-->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

                <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        <link href="css/font-styles.css" rel="stylesheet">
        <link href="css/password-req-styles.css" rel="stylesheet">

        <!-- JQuery script for data table management -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/2.0.0/dataRender/ellipsis.js"></script>
        
        
        <title>TASK Admins</title>

        <style>
            .centered {
                position: fixed;
                top: 50%;
                left: 50%;
                /* bring your own prefixes */
                transform: translate(-50%, -50%);
            }
        </style>

        <!-- Script for rendering JQuery Datatable -->
        <script>
            $(document).ready( function () {
                $('#admins_table').DataTable({
                    columnDefs: [{ 
                        targets: 2,
                        render: DataTable.render.ellipsis(120)
                    }]
                });
            } );
        </script>

    </head>

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
                            <a class= "nav-link text-light"><i>Currently Logged in as: <?=htmlspecialchars($_SESSION['admin_email'], ENT_QUOTES)?></i></a>
                        </li>
                        <li class="nav-item5">
                            <a class="text-light btn btn-secondary ml-4" href="logout.php">Logout</a>
                        </li>
                    </ul>
            </div>
        </nav>
        <!-- End of Navigation Bar -->

        <!-- Page Wrapper Div -->
        <div id="wrapper" style="margin-top: 5rem;">

            <?php include('php/message.php'); ?>

             <!-- Portal Item Description/Intention, Table Actions and Entries -->
             <div class = "position-relative">
                 <div class="position-absolute top-0 start-50 translate-middle-x">
                     <div class = "card shadow rounded text-bg-dark mb-3 text-center m-3" style="max-width:200rem; min-width: 75rem;">
                        <h1 class= "mt-4" >TASK Admins Management</h1>
                        <hr>
                        <p class = "p-4">This page will be responsible for the maintenece of the current admins who are allowed to access this site!. The following table will allow you to add, view, delete, and edit admin information.
                        </p>
                        <p class ="p-1">
                            PLEASE NOTE: If you delete your own account, <strong style="color: green;" >you will automatically be logged out of this session, and will have to create another account.</strong>
                        </p>
                        <p class = "p-1">
                            ANOTHER NOTE: Deletion of an admin account will require the password of an admin <strong style="color: red;">SUPERUSER account</strong>.
                            The current superuser account is <h3><?php echo $admin_email;?></h3>
                        </p>
                        
                     </div>

                    <!-- Container For Adding Button Control & Lesson Page Management -->
                    <div class = "container text-center">
                        <button type="button" class="btn btn-success m-1" data-bs-toggle="modal" data-bs-target="#addScreenModal">Add New Admin</button>
                    </div>

                    <!-- Table Component -->
                    <div class = "mt-5 table-responsive" style="max-width:100rem;" id = "link_wrapper">
                        <!-- Rendered Javascript code will call PHP script in modules-table-render.php -->

                        <table id="admins_table" class = "table stripe">
                            <thead>
                                <tr>
                                    <th scope="col">Admin ID</th>
                                    <th scope="col">Admin Email</th>
                                    <th scope="col">Admin Username</th>
                                    <th scope="col">Superuser Access?</th>
                                    <th scope="col">Date Created</th>
                                    <th scope="col">Last Login</th>
                                    <th scope="col">Table Actions</th>
                                </tr>
                            </thead>

                            <!-- Print out the rows taken from table of database -->
                            <tbody>
                                <tr>
                                <?php
                                    while($row = mysqli_fetch_assoc($query_run_view_table))
                                    {

                                ?>
                                    <td><?php echo $row['admin_id']; ?></td>
                                    <td><?php echo $row['admin_email']; ?></td>
                                    <td><?php echo $row['admin_username']; ?></td>
                                    <td><?php echo $row['su_access']; ?></td>
                                    <td><?php echo $row['date_created']; ?></td>
                                    <td><?php echo $row['last_login']; ?></td>
                                    <td>
                                        <a href="table_operations/admins-view.php?id=<?= $row['admin_id']; ?>" class="btn btn-sm"><i class="fa-solid fa-eye"></i></a>
                                        <a href="table_operations/admins-edit.php?id=<?= $row['admin_id']; ?>" class="btn btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>

                                        <!-- Make sure to pass ID to modal deletion screen -->
                                        <?php if($row['admin_id'] !== '1') : ?>
                                                <button data-id="<?= $row['admin_id']; ?>" type="button" data-bs-toggle="modal" name="set_id_btn" data-bs-target="#confirmationDeletionModal"><i class="fa-solid fa-trash" style="color: red;"></i></button>
                                        <?php endif; ?>
                                                
                                    </td>
                                </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- End of Table Component! -->
                 </div>
             </div>
             <!-- End of Portal Item Description/Intention, Table Actions and Entries  -->
        </div>
        <!-- End of Page Wrapper Div -->

        <!-- Modal For Adding New Entry -->
        <div class="modal fade" id="addScreenModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Admin</h1>
                        
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form action="php/admins_db_operations.php" method="POST">
                            <p><strong>NOTE: </strong>An account with superuser access has the ability to view, edit, and delete other admins. </p>
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email</label>
                                <input type="email" name="admin_email" id="admin_email" class="form-control" placeholder="abc@123.com" required>
                                <span class="mt-3" id="error_email"></span>
                            </div>
                            <div class="mb-3">
                                <label for="admin_username" class="form-label">Admin Username</label>
                                <input type="text" name="admin_username" id="admin_username" class="form-control" placeholder="johnDoeXYZ" required>
                                <span class="mt-3" id="error_username"></span>
                            </div>

                            <!-- Call Password confirmation script goes here before adding -->
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Admin Password</label>
                                <input type="password" name="admin_password" 
                                id="admin_password" 
                                class="form-control" placeholder="Enter Password" 
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                autocomplete="off"
                                required>
                            </div>

                            <!-- Display password requirements div if user does not meet them -->
                            <div id="message">
                                <p>Password must contain the following:</p>
                                <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                                <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                                <p id="number" class="invalid">A <b>number</b></p>
                                <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                            </div>
                            
                            <!-- Allow user to retype new password again -->
                            <div class="mb-3">
                                <label for="re-admin_password" class="form-label">Re-enter Admin Password</label>
                                <input type="password" name="re-admin_password" id="re-admin_password" class="form-control" autocomplete="off" placeholder="Enter Password" required>
                                <span class="mt-3" id="error_re_password"></span>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_superuser" id="is_superuser">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Give Superuser access to this account?
                                </label>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <!-- Execute SQL query when button is pressed -->
                                <a href="."><button type="submit" name = "add_admin" class="btn btn-primary">Add Admin</button></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Modal For Adding New Entry -->

        <!-- Modal screen for deletion confirmation -->
        <div class="modal fade" id="confirmationDeletionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Deletion Confirmation</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                            Are you sure you want to delete this admin? If you delete yourself, you will be logged out of this session.
                        </p>
                        <p>Please type the <strong style="color: red;" >password of the SUPERUSER</strong> to confirm this action.</p>
                    </div>

                    <!-- Process form and check if DELETE keyword is passed through before removal of row from database -->
                    <form action= "php/admins_db_operations.php" method ="POST" class="d-inline" enctype="multipart/form-data">
                        <div class ="p-3">
                            <input type ="hidden" name="admin_id" id="admin_id" value=""  class="form-control"  ></input>
                            <!-- Need to obtain current admin_id of the session -->
                            <input type ="hidden" name="session_admin_id" id="session_admin_id" value="<?=$_SESSION['admin_id'] ?>" class="form-control"  ></input>
                            <input type="password" id="set_id_btn" name="delete_admin" class="form-control" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--End of Modal screen for deletion confirmation -->

        <!--  Javascript used to retrieve the primary ID of a row for deletion-->
        <script>
            $('#confirmationDeletionModal').on('show.bs.modal', function(event){
                
                var button = $(event.relatedTarget); // This is the button that triggered the delete modal
                var getID = button.data('id');
                var modal = $(this);
                
                modal.find('#admin_id').val(getID);


            })
        </script>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="js/password_req_script.js"></script>
        <script src="js/duplicate_entry_check.js"></script>
    </body>
</html>