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

    
    
    // Adding lesson module Entry
    if(isset($_POST['add_module'])){  
        
        
        /*******Obtain metadata information specifically for image upload (used in add_module)*****/
        $targetDir = '/var/www/html/task-admin-portal/assets/images/';
        $fileName  = '';
        $fileType = '';
        $targetFilePath = '';

        //Only allow the following image types for image upload
        $allowTypes = array('jpg','png','jpeg','gif');

        // 5 MB image file limit
        $maxFileSize = 5 * 1024 * 1024;

        if(!empty($_FILES['module_image'])) {
            $fileName = basename($_FILES['module_image']['name']);
            $targetFilePath = $targetDir.$fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
            $fileSize = $_FILES["module_image"]["size"];

        }
        else{
            $_SESSION['message'] = "Image cannot be uploaded. Please try again";
        }

        /*******End of obtaining metadata information specifically for image upload (used in add_module)*****/

        $moduleName = $_POST['module_name'];
        $moduleSummary = $_POST['module_summary'];
        $moduleImage = $fileName;

        $dateCreated = date('Y-m-d');

        // Make sure file size is within range, allowed types, and able to upload image to destination
        if(!in_array($fileType, $allowTypes) || !move_uploaded_file($_FILES['module_image']['tmp_name'], $targetFilePath)){
            $_SESSION['message'] = "Image cannot be uploaded. Image must be less than 5MB and only support 'jpg','png','jpeg','gif' file types.";
        }
        else{

            $sql_stmt = $con->prepare("INSERT INTO lessonModules VALUES ('0', ?, ?, ?, ?)");
            $sql_stmt->bind_param("ssss", $moduleName, $moduleSummary, $moduleImage, $dateCreated);
            $rs = $sql_stmt->execute();
    
            if($rs){
                $_SESSION['message'] = "Module Added Successfully!";
            }
            else{
                $_SESSION['message'] = "Module Not Added! Error Encountered.";
            }
        }


    }

    // Editing Lesson Module Entry
    if(isset($_POST['edit_module'])){


        /*******Obtain metadata information specifically for image upload (used in add_module)*****/
        $targetDir = '/var/www/html/task-admin-portal/assets/images/';
        $fileName  = $_POST['original_image_name'];
        $targetFilePath = $targetDir.$fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
        $moduleImage = $fileName;

        // //Only allow the following image types for image upload
        $allowTypes = array('jpg','png','jpeg','gif');

        // 5 MB image file limit
        $maxFileSize = 5 * 1024 * 1024;

        // Instead of using empty() function, check if the admin has decided to upload a new image, otherwise upload (same) image reference/metadata
        if(is_uploaded_file($_FILES['module_image']['tmp_name'])) {
            $fileName = basename($_FILES['module_image']['name']);
            $targetFilePath = $targetDir.$fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
            $fileSize = $_FILES["module_image"]["size"];
            $moduleImage = $fileName;
        }
        else{
            $_SESSION['message'] = "Image cannot be updated. Please try again";
        }
        
        /*******End of obtaining metadata information specifically for image upload (used in add_module)*****/
   
        $moduleId =  $_POST['module_id'];
        $moduleName = $_POST['module_name'];
        $moduleSummary = $_POST['module_summary'];

        echo $moduleImage;
        echo $targetFilePath;
        echo $fileType;

        // Make sure file size is within range, allowed types, and able to upload image to destination
        if(!in_array($fileType, $allowTypes) || !move_uploaded_file($_FILES['module_image']['tmp_name'], $targetFilePath)){
            $_SESSION['message'] = "Image cannot be updated. Please try again";
        }

        $sql_stmt = $con->prepare("UPDATE lessonModules SET module_name=?, module_summary=?, module_image=? WHERE module_id=?");
        $sql_stmt->bind_param("sssi", $moduleName, $moduleSummary, $moduleImage, $moduleId);
        $rs = $sql_stmt->execute();

        // Make sure to update module name in dependent tables (Quizzes and Quiz Question tables)
        $update_module_name1 = $con->prepare("UPDATE lessonQuizzes SET module_name=? WHERE module_id=?");
        $update_module_name1->bind_param("si", $moduleName, $moduleId);
        $rs2 = $update_module_name1->execute();

        // FIX, module name should change in the Quiz question table?
        $update_module_name2 = $con->prepare("UPDATE lessonQuizQuestions SET module_name=? WHERE module_name=?");
        $update_module_name2->bind_param("ss", $moduleName, $moduleName);
        $rs2 = $update_module_name1->execute();
        
        if($rs and $rs2){
            $_SESSION['message'] = "Module Updated Successfully!";
        }
        else{
            $_SESSION['message'] = "Module Not Updated! Error Encountered.";
        }
    }

    // Delete Chosen Lesson Module Entry
    if(isset($_POST['delete_module'])){

        // Obtain confirmation text and ID if they really want to delete the module
        $confirmationText = $_POST['delete_module'];
        $moduleId = $_POST['module_id'];
        
        echo $moduleID;

        // If the strings are not equal; do not delete entry.
        if(strcmp($confirmationText,"DELETE") !== 0 ){
            $_SESSION['message'] = "Cannot delete entry; admin did not type DELETE.";
            header('Location: ../lesson-modules-table.php');
        }
        else{

            $sql_stmt = $con->prepare("DELETE FROM lessonModules WHERE module_id=?");
            $sql_stmt->bind_param("i", $moduleId);
            $rs = $sql_stmt->execute();
    
            if($rs){
                $_SESSION['message'] = "Module Deleted Successfully!";
            }
            else{
                $_SESSION['message'] = "Module Not Deleted! Error Encountered.";
            }
            
        }

    }

    // Redirect back to table list page after table operation is complete
    header('Location: ../lesson-modules-table.php');
    exit(0);

?>