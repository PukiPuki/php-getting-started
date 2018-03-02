
<?php
    session_start();
    // Connect to the database. Please change the password and dbname in the following line accordingly
    $db     = pg_connect("host=localhost port=5432 dbname=CS2102 user=postgres password=root");	
  if (isset($_POST['login'])) { 
		$check = pg_query($db, "SELECT pw, isAdmin FROM account WHERE username = '$_POST[Username]'");
		$checkData = pg_num_rows($check);

		if ($checkData > 0) {
			$hashedPw = pg_fetch_row($check); 
			
			if (password_verify($_POST['Password'],$hashedPw[0])) {
				if ($hashedPw[1] == "t") {
					$_SESSION["isAdmin"] = "True";
				} else {
				}
				$_SESSION["user"] = $_POST["Username"];
				header("Location: dashBoard.php");
				exit();   
			} else {
				$message = '<p>Wrong password!</p>';
			}
		} else {
			$message = '<p>Wrong user!</p>';
		}
    }
?> 

<!DOCTYPE html>
<html>
<title>CS2102 Assignment</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
body, html {
    height: 100%;
    line-height: 1.8;
}
.w3-bar .w3-button {
    padding: 20px;
}
</style>
<body>

<?php 
    include 'navbar.php';
?> 

<!-- Login Section !-->
<div class="w3-container w3-light-grey" style="padding:96px" id="home">
  <h3 class="w3-center">Login and be amazed!</h3>
  <div class="w3-row-padding" style="margin-top:64px padding:128px 16px">
    <div class="w3-content" align="center">
      <form action="login.php" method="POST" >
        <p><input class="w3-input w3-border" type="text" placeholder="Username" name="Username"></p>
		    <p><input class="w3-input w3-border" type="password" placeholder="Password" name="Password"></p>
          <button class="w3-button w3-black" type="submit" name = "login">
            <i class="fa fa-sign-in"></i> LOGIN
          </button>
        </p>
      </form>
      <?php echo $message; ?>
    </div>
  </div>
</div>	
 
</body>

<?php include 'footer.html' ?>