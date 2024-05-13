<?php 

class Database {
        
    static $mysqli;

    ////***********************************************************************************//
    // Name:		get_db_connection()
    // Return Value: mysqli database connection object
    // Remarks: Get the static Database connection to share among all table scripts 
    // without having multiple DB class objects.
    ////***********************************************************************************//
    public static function get_db_connection(){
        
        // Check if a table object already established a connection to the database
        if(!self::$mysqli){

            // VM database connection info
            Database::$mysqli = new mysqli("localhost", "root", "toor", "taskProject");

            // Have MySQLI report and throw errors so individual error reporting for every MySQLI function isn't required 
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }

        return self::$mysqli;
    }
}

?>