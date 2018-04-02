<?php
include 'pgconnect.php';
if (isset($_POST['add_user'])) {
    $password = password_hash($_POST[password], PASSWORD_DEFAULT);
    $query = "SELECT * FROM admin_add_user('$_POST[username]', '$password', '$_POST[phone]', '$_POST[isAdmin]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

        if ($state == 0) {
            echo "<script type='text/javascript'>
                    alert('User added!');
                    </script>";
        } else if ($state == 23505) {
            $message = "User already exists";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else if ($state == 23502) {
            $message = "You have somehow entered a null value!";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else
            echo $state;
    } else {
        echo "<script>
              alert('Update failed!');
            </script>";
    }
} else if (isset($_POST['edit_user'])) {
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $query = "SELECT * FROM admin_edit_user('$_POST[username]', '$_POST[newphone]', '$_POST[isAdmin]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

        if ($state == 0) {
            echo "<script type='text/javascript'>
                    alert('User edited!');
                    </script>";
        } else if ($state == 23505) {
            $message = "No user found";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else if ($state == 23502) {
            $message = "You have somehow entered a null value!";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else
            echo "Error Code: " . $state;
    } else {
        echo "<script>
              alert('Update failed!');
            </script>";
    }
} else if (isset($_POST['remove_user'])) {
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $query = "SELECT * FROM admin_remove_user('$_POST[username]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        echo "<script type='text/javascript'>
              alert('User removed!');
            </script>";
    } else {
        echo "<script>
              alert('User removal failed!');
            </script>";
    }
} 
        header("Location: admin.php");

?>
