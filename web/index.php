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
</div>

<div style=margin-top:64px>
    <div class="w3-container">
        <h1 class="w3-text-teal">Heading</h1>
        <p> Test test</p>
    </div>

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
    HELLO???
</div>


<script>

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

</script>

</body>
</html>

