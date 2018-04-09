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

/* <style> */
/* form { */
/*     border: 3px solid #f1f1f1; */
/* } */

/* /1* Full-width inputs *1/ */
/* input[type=text], input[type=password] { */
/*     width: 100%; */
/*     padding: 12px 20px; */
/*     margin: 8px 0; */
/*     display: inline-block; */
/*     border: 1px solid #ccc; */
/*     box-sizing: border-box; */
/* } */

/* /1* Set a style for all buttons *1/ */
/* button { */
/*     background-color: #4CAF50; */
/*     color: white; */
/*     padding: 14px 20px; */
/*     margin: 8px 0; */
/*     border: none; */
/*     cursor: pointer; */
/*     width: 100%; */
/* } */

/* /1* Add a hover effect for buttons *1/ */
/* button:hover { */
/*     opacity: 0.8; */
/* } */

/* /1* Extra style for the cancel button (red) *1/ */
/* .cancelbtn { */
/*     width: auto; */
/*     padding: 10px 18px; */
/*     background-color: #f44336; */
/* } */

/* /1* Center the avatar image inside this container *1/ */
/* .imgcontainer { */
/*     text-align: center; */
/*     margin: 24px 0 12px 0; */
/* } */

/* /1* Avatar image *1/ */
/* img.avatar { */
/*     width: 40%; */
/*     border-radius: 50%; */
/* } */

/* /1* Add padding to containers *1/ */
/* .container { */
/*     padding: 16px; */
/* } */

/* /1* The "Forgot password" text *1/ */
/* span.psw { */
/*     float: right; */
/*     padding-top: 16px; */
/* } */

/* /1* Change styles for span and cancel button on extra small screens *1/ */
/* @media screen and (max-width: 300px) { */
/*     span.psw { */
/*         display: block; */
/*         float: none; */
/*     } */
/*     .cancelbtn { */
/*         width: 100%; */
/*     } */
/* } */

/* </style> */
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
        <span class="psw">Forgot <a href="#">password?</a></span>
        <label>
            <input type="checkbox" name="remember"> Remember me
        </label>
    </div>

    <div class="row">
  <button class="btn waves-effect waves-light" type="submit" name="login">Login</button>
    </div>
        
    <div class="row">
    <button type="button" class="cancelbtn">Cancel</button>
    </div>
</div>
</form> 
<?php
    echo $message;
?>
</body>
</html>
