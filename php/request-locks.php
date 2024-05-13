<?php 

    session_start();

    ////***********************************************************************************//
    // Name:		request_admin_access(string attemptedAccess).
    // Parameters:	attemptedAccess = "events", "materials", "news", "campusResources", "reports", "users", or "admins"
    // Return Value: None
    // Remarks:		This function either grants access to the request and allows it to continue
    //              OR the sets the HTTP response header to an error code and blocks the request 
    ////***********************************************************************************//
    function request_admin_access($attemptedAccess){
    
        $securityOn = false;

        if($securityOn){

            if(!isset($_SESSION["adminID"]) || !isset($_SESSION["accessLevels"][$attemptedAccess]) || $_SESSION["accessLevels"][$attemptedAccess] !== 1){

                if(isset($_SESSION["adminID"])){
                    // Admin is logged in but they're not authorized for the request
                    header('HTTP/1.0 403 Forbidden', true, 403);
                }
                else{
                    // Admin is not currently logged in at all
                    header('HTTP/1.0 401 Unauthorized', true, 401);
                }
                
                exit();
            }
        }
        
    
    }
    
?>
