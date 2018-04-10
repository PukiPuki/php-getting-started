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
<link type="text/css" rel="stylesheet" href="./style/css/materialize.min.css" media="screen,projection">
<link href="https://fonts.googleapis.com/icon?family=Inconsolata" rel="stylesheet">
</head>
<body>
<?php
    include 'navbar.php';

    if (isset($_POST['login'])) {
        $query = "SELECT password, isAdmin 
                    FROM users
                    WHERE username='$_POST[uname]'";
        $check = pg_query($pg_conn, $query);
        $valid = pg_num_rows($check);

        if ($valid > 0) {
            $data = pg_fetch_row($check);

            if (password_verify($_POST[psw], $data[0])) {
                if ($data[1] == "t") {
                    $_SESSION[isAdmin] = True;
                } else {
                    $_SESSION[isAdmin] = False;    
                }
                $_SESSION[user] = $_POST[uname];
                header("Location: index.php");
                exit();
            } else {
                $message = '<p> Wrong username/password!</p>';
        echo "<script type='text/javascript'>alert('$message');</script>";
            }
        } else {
           $message = '<p> Wrong username/password!</p>';
        }
    } 
?>

<div class="container">
 <form action="login.php" method="POST">
    <div class="row input-field col s12">
        <input type="text" id="uname" name="uname" class="validate">
        <label for="uname"><b>Username</b></label>
    </div>

    <div class="row input-field col s12">
        <input type="password" id="psw" name="psw" class="validate">
        <label for="psw"><b>Password</b></label>
    </div>

    <div class="row">
            <label for="remember">
            <input type="checkbox" id="remember" /> 
                <span> Remember me </span>
            </label>
    </div>

    <div class="row container center">
  <button class="btn waves-effect waves-light" type="submit" name="login">Login</button>
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
