<!DOCTYPE html>
<?php
    session_start();
    include 'pgconnect.php';
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="./style/css/materialize.css"
<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

<div class="row">
 <form class="col s12" action="login.php" method="POST">
    <div class="row">
    <label for="uname"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="uname" class="validate">
    </div>

    <div class="row">
    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" class="validate">
    </div>
    <div class="row">
        <p>
        <label>
            <input type="checkbox" name="remember" /> 
            <span> Remember me  </span>
        </label>
        </p>
    </div>

    <div class="row container">
  <button class="btn waves-effect waves-light" type="submit" name="login">Login</button>
    </div>
        
    <div class="row container">
    <button type="button" class="cancelbtn">Cancel</button>
    </div>
</div>
</form> 
<?php
    echo $message;
?>
</body>
</html>
