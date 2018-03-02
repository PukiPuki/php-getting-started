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
<?php
    function pg_connection_string_from_database_url() {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . "sslmode=require"; # <- you may want to add sslmode=require there too
    }
    include 'navbar.php';
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $result = pg_query($pgconn, "SELECT name from auth_user");
    $data = pg_fetch_assoc($result);
    echo $data["username"];
?>

  /* <!-- Pagination --> */
  /* <div class="w3-center w3-padding-32"> */
  /*   <div class="w3-bar"> */
  /*     <a class="w3-button w3-black" href="#">1</a> */
  /*     <a class="w3-button w3-hover-black" href="#">2</a> */
  /*     <a class="w3-button w3-hover-black" href="#">3</a> */
  /*     <a class="w3-button w3-hover-black" href="#">4</a> */
  /*     <a class="w3-button w3-hover-black" href="#">5</a> */
  /*     <a class="w3-button w3-hover-black" href="#">»</a> */
  /*   </div> */
  /* </div> */
    if (isset($_SESSION['user'])) {

    }



<!-- END MAIN -->
</div>

<script>

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

</script>

</body>
</html>

