<?php
    //When user logs out of the portal, make sure to destroy current session and redirect the admin to the login page.
    //Recall that sessions are used to determine whether the user is logged in or not, so by removing them, the user will not be logged in.
    session_start();
    session_destroy();
    // Redirect to the login page;
    header('Location: login.php');
?>