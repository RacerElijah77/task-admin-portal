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

    
    // Adding lesson module page Entry
    if(isset($_POST['add_module_page'])){  
     
        /*******Obtain metadata information specifically for image upload (used in add_module)*****/
        $targetImageDir = '/var/www/html/task-admin-portal/assets/images/';
        $fileImageName  = '';
        $fileImageType = '';
        $targetImageFilePath = '';
        

        //Only allow the following image types for image upload
        $allowImageTypes = array('jpg','png','jpeg','gif');

        // 5 MB image file limit
        $maxImageFileSize = 5 * 1024 * 1024;

        if(!empty($_FILES['module_info_image'])) {
            $fileImageName = basename($_FILES['module_info_image']['name']);
            $targetImageFilePath = $targetImageDir.$fileImageName;
            $fileImageType = pathinfo($targetImageFilePath,PATHINFO_EXTENSION); 
            $fileImageSize = $_FILES["module_info_image"]["size"];

        }
        else{
            $_SESSION['message'] = "Image cannot be uploaded. Please try again";
        }

        /*******End of obtaining metadata information specifically for image upload (used in add_module)*****/

        /*******Obtain metadata information specifically for video upload (used in add_module)*****/
        $targetVideoDir = '/var/www/html/task-admin-portal/assets/videos/';
        $fileVideoName  = '';
        $fileVideoType = '';
        $targetVideoFilePath = '';
        $fileVideoSize = '';

        //Only allow the following image types for video upload
        $allowVideoTypes = array('mp4','avi','mov','mpeg');

        // 500 MB video file limit
        $maxVideoFileSize = 500 * 1024 * 1024;

        if(!empty($_FILES['module_info_video'])) {
            $fileVideoName = basename($_FILES['module_info_video']['name']);
            $targetVideoFilePath = $targetVideoDir.$fileVideoName;
            $fileVideoType = pathinfo($targetVideoFilePath,PATHINFO_EXTENSION); 
            $fileVideoSize = $_FILES["module_info_video"]["size"];

        }
        else{
            $_SESSION['message'] = "Video cannot be uploaded. Please try again";
        }

        /*******End of obtaining metadata information specifically for video upload (used in add_module)*****/

        $selectedModuleID = $_POST['module-choice'];
        $moduleInfoText = $_POST['module_info_text'];
        $modulePageHeader = $_POST['module_page_header'];

        $dateCreated = date('Y-m-d');

        // echo "Selected Module ID:" . $selectedModuleID . "\n" ;
        // echo "Module page Header:" . $modulePageHeader . "\n";

        // echo "Video Name: " . $fileVideoName;
        // echo "Image Name: " . $fileImageName;

        echo "Array for Image Type: " . in_array($fileImageType, $allowImageTypes) . "\n";
        echo "Image uploaded? " . move_uploaded_file($_FILES['module_info_image']['tmp_name'], $targetImageFilePath) . "\n";
        echo "Array for Video Type: " . in_array($fileVideoType, $allowVideoTypes) . "\n";
        echo "Video uploaded? " . move_uploaded_file($_FILES['module_info_video']['tmp_name'], $targetVideoFilePath);

        // Make sure file size is within range, allowed types, and able to upload image/video to destination
        // if(!in_array($fileImageType, $allowImageTypes) || !move_uploaded_file($_FILES['module_info_image']['tmp_name'], $targetImageFilePath)){
        //     $_SESSION['message'] = "Image cannot be uploaded. Image must be less than 5MB and only support 'jpg','png','jpeg','gif' file types.";
        // }
        // elseif(!in_array($fileVideoType, $allowVideoTypes) || !move_uploaded_file($_FILES['module_info_video']['tmp_name'], $targetVideoFilePath)){
        //     $_SESSION['message'] = "Video cannot be uploaded. Video must be less than 500MB and only support 'mp4','avi','mov','mpeg' file types.";
        // }
        // else{

            // FIX: Sql query is not working!
            $sql_stmt = $con->prepare("INSERT INTO lessonModulesPages VALUES ('0', ?, ?, ?, ?, ?, ?)");
            $sql_stmt->bind_param("sssiss", $moduleInfoText, $fileImageName, $fileVideoName, $selectedModuleID, $dateCreated, $modulePageHeader);
            $rs = $sql_stmt->execute();

            if($rs){
                echo "Good job!";
                $_SESSION['message'] = "Module Page Added Successfully!";
            }
            else{
                echo "Bad Job!";
                $_SESSION['message'] = "Module Page Not Added! Error Encountered.";
            }
        // }

    }

    // Editing Lesson Module page Entry
    if(isset($_POST['edit_module_page'])){


        /*******Obtain metadata information specifically for image upload (used in add_module)*****/
        $targetPhotoDir = '/var/www/html/task-admin-portal/assets/images/';
        $fileName  = '';
        $fileType = '';
        $targetFilePath = '';

        //Only allow the following image types for image upload
        $allowTypes = array('jpg','png','jpeg','gif');

        // 5 MB image file limit
        $maxFileSize = 5 * 1024 * 1024;

        if(!empty($_FILES['module_image'])) {
            $fileName = basename($_FILES['module_image']['name']);
            $targetFilePath = $targetPhotoDir.$fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
            $fileSize = $_FILES["module_image"]["size"];

        }
        else{
            $_SESSION['message'] = "Image cannot be updated. Please try again";
        }

        /*******End of obtaining metadata information specifically for image upload (used in add_module)*****/
   
        $moduleId =  $_POST['module_id'];
        $moduleName = $_POST['module_name'];
        $moduleSummary = $_POST['module_summary'];
        $moduleImage = $fileName;

        // Make sure file size is within range, allowed types, and able to upload image to destination
        if($fileSize > $maxFileSize || !in_array($fileType, $allowTypes) || !move_uploaded_file($_FILES['module_image']['tmp_name'], $targetFilePath)){
            $_SESSION['message'] = "Image cannot be updated. Please try again";
        }

        $sql_stmt = $con->prepare("UPDATE lessonModules SET module_name=?, module_summary=?, module_image=? WHERE module_id=?");
        $sql_stmt->bind_param("sssi", $moduleName, $moduleSummary, $moduleImage, $moduleId);
        $rs = $sql_stmt->execute();

        if($rs){
            $_SESSION['message'] = "Module Updated Successfully!";
        }
        else{
            $_SESSION['message'] = "Module Not Updated! Error Encountered.";
        }
    }

    // Delete Chosen Lesson Module page Entry
    if(isset($_POST['delete_module_page'])){

        // Obtain confirmation text and ID if they really want to delete the module
        $confirmationText = $_POST['delete_module'];
        $moduleId = $_POST['module_id'];

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

    // // Redirect back to table list page after table operation is complete
    header('Location: ../lesson-module-page-table.php');
    exit(0);

?>