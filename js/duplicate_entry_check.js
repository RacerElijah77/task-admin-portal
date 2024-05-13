// This  script will be used to check before form submission if new entries already exist in database & password match
// For example, we can render a warning message in the form front-end to the user saying that the email/username is already taken
// Ajax will be used to send HTTP POST requests without having the refresh the page (show warning message in real time)
$(document).ready(function () {

    // Admin Email HTML reference located in admins-table.php
    $('#admin_email').keyup(function (e) {

        var email = $("#admin_email").val();

        $.ajax({
            type: "POST",
            url: "php/admins_db_operations.php",
            data: {
                    'duplicate_email_check': 1,
                    'email_check': email,
            },
            success: function (response){
                $("#error_email").html(response);
            }
        });
    });

    // Admin Username HTML reference located in admins-table.php
    $('#admin_username').keyup(function (e) {

        var username = $("#admin_username").val();

        $.ajax({
            type: "POST",
            url: "php/admins_db_operations.php",
            data: {
                    'duplicate_username_check': 1,
                    'username_check': username,
            },
            success: function (response){
                $("#error_username").html(response);
            }
        });
    });

    // Admin Password HTML reference located in admins-table.php
    $('#admin_password, #re-admin_password').keyup(function (e) {

        var password = $("#admin_password").val();
        var re_password = $("#re-admin_password").val();

        $.ajax({
            type: "POST",
            url: "php/admins_db_operations.php",
            data: {
                    'mismatch_password_check': 1,
                    'raw_password': password,
                    're_password': re_password,
            },
            success: function (response){
                $("#error_re_password").html(response);
            }
        });
    });

});