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
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif;}
</style>
<body>
<div>
<?php
    include 'navbar.php';
?>
<div>
<!-- Header with full-height image -->
<header class="bgimg-1 w3-display-container w3-grayscale-min" id="home">
  <div class="w3-display-left w3-text-black" style="padding:48px">
    TEST <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    TEST
    <span class="w3-jumbo w3-hide-small">Start something that matters!</span><br>
    <span class="w3-xxlarge w3-hide-large w3-hide-medium">Start something that matters</span><br>
    <span class="w3-large">Stop doing everything yourself and get help from others.</span>
    <p><a href="Registration.php" class="w3-button w3-black w3-padding-large w3-large w3-margin-top w3-opacity w3-hover-opacity-off">Register now and start today</a></p>
    HUH
  </div>
</header>

<div class="container">

<?php
    function pg_connection_string_from_database_url() {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . "sslmode=require"; # <- you may want to add sslmode=require there too
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $result = pg_query($pgconn, "SELECT username from auth_user WHERE username='limys'");
    $data = pg_fetch_assoc($result);
    echo $data["username"];
    }
    if (isset($_SESSION['user'])) {

    }

?>

</div>


<script>

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

</script>

</body>
</html>

