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

    $query = "SELECT * FROM select_active_transactions()";

    if (isset($_POST['get_all_button'])) {
      $pg_conn = pg_connect(pg_connection_string_from_database_url())
          or die('Could not connect:' . pg_last_error());
      $query = "SELECT * FROM select_active_transactions()";
      $result = pg_query($pg_conn, $query) or die('Query failed: '. pg_last_error());

      echo "<script> alert(\"yourmother\"); </script>";
      echo "<script> console.log(\"yourmother\"); </script>";

      if ($result) {
          $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
      }
    }
?>
<script type="text/javascript">
      var test = "<?php echo $result ?>"
      console.log(test);
      var query = "<?php echo $query ?>"
      console.log(query);
</script>
<h1 class="w3-text-teal">Search</h1>
 <form method="GET">
    <button type="submit" name="get_all_button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
      Get All</button>
 </form>

</div>

<script>
// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");
</script>
</body>
</html>
