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

    if ($_POST[psw] == $_POST[cfmpsw]) {
        $pg_conn = pg_connect(pg_connection_string_from_database_url())
            or die('Could not connect:' . pg_last_error());
        $password = password_hash($_POST[psw],PASSWORD_DEFAULT);
        $query = "SELECT admin_add_user('$_POST[uname]', $psw, '$_POST[phn]', 'False')";
        $result = pg_query($pg_conn, $query) or die('Query failed: '. pg_last_error());
        
        if ($result) {
            $state = pg_result_error_field($result,PGSQL_DIAG_SQLSTATE);

            if ($state == 0) {
					$_SESSION['user'] = $_POST[uname];
					$_SESSION['phone'] = $_POST[phn];
					$_SESSION["isAdmin"] = "False";
					header("Location: index.php");
					exit();
            } else if ($state  == 23505) {
                $message = "Your username has already been taken!";
            } else if ($state == 23502) {
                $message = "You have somehow entered a null value!";
            } else
                echo $state;
            }
    } else {
        $message = '<p> Passwords do not match!</p>';
    }
?>

<div style="margin-top:43px">
 <form action="signup.php" method="POST">

  <div class="container">
    <label for="uname"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="uname" required>

    <label for="psw"><b>New Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" required>
    <label for="cfmpsw"><b>Confirm Password</b></label>
    <input type="password" placeholder="Confirm Password" name="cfmpsw" required>

    <label for="psw"><b> Phone Number</b></label>
    <input type="text" placeholder="Enter Phone Number" name="phn" required>

    <button type="submit">Signup</button>
    <label>
    </label>
  </div>

  <div class="container" style="background-color:#f1f1f1">
    <button type="button" class="cancelbtn">Cancel</button>
  </div>
</div>
</form> 
<?php
    echo $message;
?>
</body>
</html>
