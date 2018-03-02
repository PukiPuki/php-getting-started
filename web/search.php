<!DOCTYPE html>
<?php
    session_start();
?>
<html>
<title>Stuff Sharing (CS2102 Project)</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif;}
</style>
<body>
<div>
<?php
    include 'navbar.php';
?>
</div>

<div style=margin-top:43px>
    <div class="w3-container">
        <h1 class="w3-text-teal">Search</h1>
    </div>
<?php
    include 'navbar.php';

    function pg_connection_string_from_database_url() {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
    }

    if (isset($_POST['signup'])) {

        if ($_POST[psw] == $_POST[cfmpsw]) {
            $pg_conn = pg_connect(pg_connection_string_from_database_url())
                or die('Could not connect:' . pg_last_error());
            $password = password_hash($_POST[psw],PASSWORD_DEFAULT);
            $query = "SELECT * FROM admin_add_user('$_POST[uname]', '$password', '$_POST[phn]', 'False')";
            pg_send_query($pg_conn, $query) or die('Query failed: '. pg_last_error());
            $result = pg_get_result($pg_conn);
            
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
    }
?>
<h1 class="w3-text-teal">Search</h1>
      
 <form action="search.php" method="GET">
    <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
      Get All</button>
 </form>

</div>

<script>
// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");
</script>
</body>
</html>
