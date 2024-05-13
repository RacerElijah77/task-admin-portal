
<?php
    require_once "cors.php";
    require_once "database-handler.php";

    $statusMsg = '';
    $targetDir = '/var/www/html/task-admin-portal/assets/images/';
    $fileName  = '';
    $fileType = '';
    $targetFilePath = '';

    // Obtain text information from the input boxes
    if(isset($_POST['submit'])){  
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $sampleValue = $_POST['sampleValue'];
    }

    // Obtain metadata information for selected image from form (name=image)
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir.$fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
        $fileSize = $_FILES["image"]["size"];
    }

    // Call this function from the database-handler.php script import
    $con = Database::get_db_connection();

    // to ensure that the connection is made
    if (!$con)
    {
        die("Connection failed!" . mysqli_connect_error());
    }
    
    //Only allow the following image types for image upload
    $allowTypes = array('jpg','png','jpeg','gif');

    // 5 MB image file limit
    $maxsize = 5 * 1024 * 1024;

    if(in_array($fileType, $allowTypes)){

        if($fileSize <= $maxsize && move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)){ 
            
            // Used prepared statement version for query execution to prevent SQL injection
            // Take note on this for paper/poster
            $sql_stmt = $con->prepare("INSERT INTO demo_table_2 VALUES ('0', ?, ?, ?, ?)");
            $sql_stmt->bind_param("ssis", $firstName, $lastName, $sampleValue, $fileName);
            $rs = $sql_stmt->execute();

            if($rs){
                echo "Query Executed!";
            }
            else{
                echo "Query not executed!";
            }
        }
        else{
            echo "Error was expected when uploading image- File size is over 5MB.";
        }
    }
    else{
        echo "Cannot process query: need to have compatible file types (JPG, PNG, JPEG, GIF) allowed";
    }
    
    // Make sure to close the connection
    mysqli_close($con);

?>