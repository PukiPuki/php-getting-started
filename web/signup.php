<!DOCTYPE html>
<?php
    session_start();
    include 'pgconnect.php';
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="./style/css/materialize.css"
<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>
<?php
    include 'navbar.php';


    if (isset($_POST['signup'])) {

        if ($_POST[psw] == $_POST[cfmpsw]) {
            $password = password_hash($_POST[psw],PASSWORD_DEFAULT);
            $query = "SELECT * FROM admin_add_user('$_POST[uname]', '$password', '$_POST[phn]', 'False')";
            pg_send_query($pg_conn, $query);
            $result = pg_get_result($pg_conn);
            
            if ($result) {
                $state = pg_result_error_field($result,PGSQL_DIAG_SQLSTATE);

                if ($state == 0) {
	    				$_SESSION['user'] = $_POST[uname];
	    				$_SESSION['phone'] = $_POST[phn];
	    				$_SESSION["isAdmin"] = False;
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
    }
?>

<div style="margin-top:43px">
 <form action="signup.php" method="POST">

  <div>
    <div class="row">
        <div class="input-field col s6">
             <input id="username" type="text" name="uname "class="validate">
            <label for="username">Username</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6">
             <input id="password" type="text" name="psw "class="validate">
            <label for="password">New Password</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6">
             <input id="cfmpass" type="text" name="cfmpsw "class="validate">
            <label for="cfmpass">Confirm Password</label>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s6">
             <input id="phone" type="text" name="phn "class="validate">
            <label for="phone">Phone Number</label>
        </div>
    </div>

    <div class="row container center">
        <button class="btn waves-effect waves-light" type="submit" name="signup">Sign Up</button>
    </div>
        
    <div class="row container center">
        <button class="btn waves-effect waves-light red" type="button" class="cancelbtn">Cancel</button>
    </div>


  </div>

</div>
</form> 
<?php
    echo $message;
?>
</body>
</html>
