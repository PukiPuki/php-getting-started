<!DOCTYPE html>
<?php
    session_start();
    include 'pgconnect.php';
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Compiled and minified CSS -->
<link type="text/css" rel="stylesheet" href="./style/css/materialize.css" media="screen,projection" />
<link href="https://fonts.googleapis.com/icon?family=Inconsolata" rel="stylesheet">
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
<div class="row">
    <div class="input-field col s12">
        <input id="test" type="text">
            <label for="test"> Test </label>
    </div>
</div>

<div id="input" class="row">
    <form action="signup.php" method="POST" class="col s12">
        <div class="row">
            <div class="input-field col s12">
                 <input id="username" type="text" name="uname" class="validate">
                <label for="username">Username</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                 <input id="password" type="password" name="psw" class="validate">
                <label for="password">New Password</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                 <input id="cfmpass" type="password" name="cfmpsw" class="validate">
                <label for="cfmpass">Confirm Password</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                 <input id="phone" type="text" name="phn" class="validate">
                <label for="phone">Phone Number</label>
            </div>
        </div>
    
        <div class="row container center">
            <button class="btn waves-effect waves-light" type="submit" name="signup">Sign Up</button>
        </div>
            
        <div class="row container center">
            <button class="btn waves-effect waves-light red" type="button" class="cancelbtn">Cancel</button>
        </div>
    </form> 
</div>
<?php
    echo $message;
?>
 <!--JavaScript at end of body for optimized loading-->
      <script type="text/javascript" src="./style/js/materialize.min.js"></script>
</body>
</html>
