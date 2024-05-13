<!-- PHP script to generate alert message banner when a database/user operation is triggered -->
<?php
    if(isset($_SESSION['message'])) :
?>

    <div class="alert alert-warning alert-dismissible fade show container justify-content-center fixed-top" role="alert">
        <strong>Operation Detected:</strong>
        <span><?= $_SESSION['message']?></span>
        <button type="button" class = "btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

<?php
    unset($_SESSION['message']);
    endif;
?>