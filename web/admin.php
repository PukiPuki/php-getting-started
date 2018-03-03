<!DOCTYPE html>
<?php
    session_start();
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
form {
    border: 3px solid #f1f1f1;
}

/* Full-width inputs */
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Set a style for all buttons */
button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

/* Add a hover effect for buttons */
button:hover {
    opacity: 0.8;
}

/* Extra style for the cancel button (red) */
.cancelbtn {
    width: auto;
    padding: 10px 18px;
    background-color: #f44336;
}

/* Center the avatar image inside this container */
.imgcontainer {
    text-align: center;
    margin: 24px 0 12px 0;
}

/* Avatar image */
img.avatar {
    width: 40%;
    border-radius: 50%;
}

/* Add padding to containers */
.container {
    padding: 16px;
}

/* The "Forgot password" text */
span.psw {
    float: right;
    padding-top: 16px;
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
    span.psw {
        display: block;
        float: none;
    }
    .cancelbtn {
        width: 100%;
    }
}

</style>
</head>
<body>
<?php
    include 'navbar.php';

    function pg_connection_string_from_database_url() {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
    }
    if (!$_SESSION[isAdmin]){
        header('Location:index.php');
    } else {

    }

    #TODO: Add checking of valid username, etc
    if (isset($_POST['add_user'])) {
        $pg_conn = pg_connect(pg_connection_string_from_database_url());
        $password = password_hash($_POST[password],PASSWORD_DEFAULT);
        $query = "SELECT * FROM admin_add_user('$_POST[username]', '$password', '$_POST[phone]', '$_POST[isAdmin]')";
        pg_send_query($pg_conn, $query);
        $result = pg_get_result($pg_conn);

        if ($result) {
                $state = pg_result_error_field($result,PGSQL_DIAG_SQLSTATE);

                if ($state == 0) {
                    echo "<script type='text/javascript'>
                    alert('User added!');
                    </script>"; 
                } else if ($state  == 23505) {
                    $message = "User already exists";
                } else if ($state == 23502) {
                    $message = "You have somehow entered a null value!";
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
                $state = pg_result_error_field($result,PGSQL_DIAG_SQLSTATE);

                if ($state == 0) {
                    echo "<script type='text/javascript'>
                    alert('User edited!');
                    </script>"; 
                } else if ($state  == 23505) {
                    $message = "No user found";
                } else if ($state == 23502) {
                    $message = "You have somehow entered a null value!";
                } else
                    echo $state;
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
    } else {

    }
?>

<div style="margin-top:43px">
 <div>
 <h1 class="w3-text-teal"> User Control </h1>
 <div class="container">
  <h2 class="w3-text-teal"> Add User </h2>
  <form action="admin.php" method="POST">
  <div class="container">
    <label for="username"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="username" required>
    <label for="password"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="password" required>
    <label for="phone"><b>Phone Number</b></label>
    <input type="text" placeholder="Enter Phone Number" name="phone" required>
    <label for="isAdmin"><b>Admin Privileges</b></label>
    <input type="hidden" name="isAdmin" value="0"> </input>
    <input type="checkbox" name="isAdmin" value="1"> Admin </input>
    <button type="submit" name= "add_user">Add User</button>
  </div>
</div>
</form> 
<?php
    echo $message;
?>
</body>
</html>
 <form action="admin.php" method="POST">

  <div class="container">
    <h2 class="w3-text-teal"> Edit User </h2>
    <label for="username"><b>Username</b></label>
    <input type="text" placeholder="Enter User to Edit" name="username" required>

    <label for="newphone"><b>New Phone Number</b></label>
    <input type="text" placeholder="Enter New Phone Number" name="newphone" required>

    <label for="isAdmin"><b>Admin Privileges</b></label>
    <input type="hidden" name="isAdmin" value="0"> </input>
    <input type="checkbox" name="isAdmin" value="1"> Admin </input>
    <button type="submit" name= "edit_user">Edit User</button>
  </div>
</div>
</form> 
<?php
    echo $message;
?>
 <div class="container">
  <h2 class="w3-text-teal"> Remove User </h2>
 <form action="admin.php" method="POST">

  <div class="container">
    <label for="username"><b>Username</b></label>
    <input type="text" placeholder="Enter User to Remove" name="username" required>
    <button type="submit" name= "remove_user">Remove User</button>
  </div>
</div>
</form> 
<?php
    echo $message;
?>
</body>
</html>
