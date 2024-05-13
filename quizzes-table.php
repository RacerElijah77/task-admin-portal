<?php
    require_once "php/database-handler.php";
  
    // Make sure to establish database connection first!
    session_start();
    if(!isset($_SESSION['loggedin'])){
        header('Location: login.php');
        exit(0);
    }
    
    $con = Database::get_db_connection();
    
    // SQL query to view all entries in selected table;
    $query_view_table = "SELECT * FROM lessonQuizzes";
    $query_run_view_table = mysqli_query($con, $query_view_table);
    
    // Used to display the drop down menu of list of lesson modules when adding a quiz
    $query_drop_down_modules = "SELECT module_id, module_name FROM lessonModules";
    $query_run_drop_down_modules = mysqli_query($con, $query_drop_down_modules);

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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

                <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        <link href="css/font-styles.css" rel="stylesheet">

        <!-- JQuery script for data table management -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/2.0.0/dataRender/ellipsis.js"></script>
        
        
        <title>TASK Lesson Quizzes</title>

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
                $('#quizzes_table').DataTable({
                    columnDefs: [{ 
                        targets: 3,
                        render: DataTable.render.ellipsis(80)
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
                        <h1 class= "mt-4" >TASK Lesson Quizzes Management</h1>
                        <hr>
                        <p class = "p-4">This page will be responsible for the maintenece of the Quiz section for a module. Ideally, 
                            <strong style="color: red;">there will be a quiz entry PER module.</strong> Management of overall quiz description,
                            number of questions, and time elapsed (time to complete the quiz) will be viewed here. 
                        </p>
                         <p class ="p-1">
                            PLEASE NOTE: If you want to add quiz questions, select the <strong style="color: orange;" >Manage Quiz Question Content</strong> button below. You will be navigated to another page to manage quiz question content for all modules.
                        </p>
                     </div>

                    <!-- Container For Adding Button Control & Lesson Page Management -->
                    <div class = "container text-center">
                        <button type="button" class="btn btn-success m-1" data-bs-toggle="modal" data-bs-target="#addScreenModal">Add Quiz</button>
                        <a href="quiz-question-table.php"><button type="button" class="btn btn-warning m-1">Manage Quiz Question Content</button></a>
                    </div>

                    <!-- Table Component -->
                    <div class = "mt-5 table-responsive" style="max-width:100rem;" id = "link_wrapper">
                        <!-- Rendered Javascript code will call PHP script in modules-table-render.php -->

                        <table id="quizzes_table" class = "table stripe">
                            <thead>
                                <tr>
                                    <th scope="col">Quiz ID</th>
                                    <th scope="col">Module ID</th>
                                    <th scope="col">Module Name</th>
                                    <th scope="col">Quiz Description</th>
                                    <th scope="col"># of Questions</th>
                                    <th scope="col">Elapsed Time (minutes)</th>
                                    <th scope="col">Max Score</th>
                                    <th scope="col">Date Created</th>
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
                                    <td><?php echo $row['quiz_id']; ?></td>
                                    <td><?php echo $row['module_id']; ?></td>
                                    <td><?php echo $row['module_name']; ?></td>
                                    <td style="max-width: 20rem; "><div style="white-space: nowrap; overflow-x: hidden;"><?php echo $row['quiz_description']; ?></div></td>
                                    <td><?php echo $row['num_questions']; ?></td>
                                    <td><?php echo $row['elapsed_time']; ?></td>
                                    <td><?php echo $row['max_possible_score']; ?></td>
                                    <td><?php echo $row['date_created']; ?></td>
                                    <td>
                                        <a href="table_operations/quizzes-view.php?id=<?= $row['quiz_id']; ?>" class="btn btn-sm"><i class="fa-solid fa-eye"></i></a>
                                        <a href="table_operations/quizzes-edit.php?id=<?= $row['quiz_id']; ?>" class="btn btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>

                                        <!-- Make sure to pass ID to modal deletion screen -->
                                        <button data-id="<?= $row['quiz_id']; ?>" type="button" data-bs-toggle="modal" name="set_id_btn" data-bs-target="#confirmationDeletionModal"><i class="fa-solid fa-trash" style="color: red;"></i></button>

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
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Quiz</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form action="php/quizzes_db_operations.php" method="POST" enctype="multipart/form-data" >
                            <p><strong>NOTE: </strong>Please make sure to select which module you want to add the quiz to!</p>
                            <hr>
                            <div class = "mb-3">
                                <!-- Code for dropdown menu to select which module ID this page should go to -->
                                <div class="dropdown">
                                    <label for="module_info_text" class="form-label"><strong>Select Corresponding Module</strong></label>
                                    <select class="form-select" name="module-choice">
                                        <!-- Retrieve list of corresponding module on where to add this page -->
                                        <?php 
                                            while($row = mysqli_fetch_assoc($query_run_drop_down_modules))
                                            {
                                        ?>
                                                <!-- Make suer to pass both the module ID and module name for adding new quiz entry SQL query -->
                                                <option value="<?php echo $row['module_id']?>-<?php echo $row['module_name']?>" >Module ID: <?php echo $row['module_id']?> | Module Name: <?php echo $row['module_name']?></option>
                                        <?php 
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- <p>ADD ASAP: Add logic for checking if selected module has ALREADY a quiz present within modal screen</p> -->
                            
                            <div class="mb-3">
                                <label for="quiz_description" class="form-label">Quiz Description</label>
                                <textarea class="form-control" name="quiz_description" id="quiz_description" rows="7" required></textarea>
                                <!-- <div class="form-text">Lesson Module Summary</div> -->
                            </div>

                            <!-- Remove later; number of questions should be updated based on the # of questions added in another table -->
                            <!-- <div class="mb-3">
                                <label for="num_questions" class="form-label"># of Questions</label>
                                <input type="text" name="num_questions" id="num_questions" class="form-control" placeholder="ex: 5" required>
                            </div> -->

                            <div class="mb-3">
                                <label for="elapsed_time" class="form-label">Elapsed Time (Time to complete in <strong>minutes</strong>)</label>
                                <input type="text" name="elapsed_time" id="elapsed_time" class="form-control" placeholder="ex: 10" required>
                            </div>

                            <!-- <div class="mb-3">
                                <label for="max_possible_score" class="form-label">Max Possible Score</label>
                                <input type="text" name="max_possible_score" id="max_possible_score" class="form-control" placeholder="ex: 15" required>
                            </div> -->
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <!-- Execute SQL query when button is pressed -->
                                <a href="."><button type="submit" name = "add_quiz" class="btn btn-primary">Add Quiz to selected Learning Module</button></a>
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
                            Are you sure you want to delete this entry? Deletion of a quiz
                            will also delete all associated <strong>quiz questions and answers!</strong>
                            Please type <strong style="color: red;" >DELETE</strong> (case sensitive) to confirm this action.
                        </p>
                    </div>

                    <!-- Process form and check if DELETE keyword is passed through before removal of row from database -->
                    <form action= "php/quizzes_db_operations.php" method ="POST" class="d-inline" enctype="multipart/form-data">
                        <div class ="p-3">
                            <input type ="hidden" name="quiz_id" id="quiz_id" value=""  class="form-control"  ></input>
                            <input type="text" id="set_id_btn" name="delete_quiz" class="form-control" required>
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
                
                modal.find('#quiz_id').val(getID);

            })
        </script>
        
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>

