<!DOCTYPE html>
<?php
    session_start();
    $username = $_SESSION['user'];
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

<div style=margin-top:43px>
    <div class="w3-container">
        <h1 class="w3-text-teal">Heading</h1>
    </div>

<?php
    function pg_connection_string_from_database_url() {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
    }
    $pg_conn = pg_connect(pg_connection_string_from_database_url())
        or die('Could not connect:' . pg_last_error());
    //$query = "SELECT * FROM all_current_loans_accepted('$username')";
    $query = "SELECT * FROM bids";
    $result = pg_query($pg_conn, $query) or die('Query failed: '. pg_last_error());

    if(!$result) {
    	echo '<p>You have nothing loaned out!</p>';
    }
    while($row = pg_fetch_assoc($result)) {
    	echo '<p>1</p>'
    	/*echo '<div class="panel panel-info">
    			<div class="panel-heading"><b>'.Your Loans.'</b></div>
    			<div class="panel-body">
    				Item: '.$row["itemName"].'</br>
    				BidderName: '.$row["bidderName"].'</br>
    				ReturnDate: '.$row["returnDate"].'</br>
    			</div>
    		</div>';*/
    }
?>

<script type="text/javascript">
      var query = "<?php echo $query ?>"
</script>

</div>


<script>

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

</script>
</body>
</html>

