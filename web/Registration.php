<!DOCTYPE html>
<?php
	session_start();
?>
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
	
  	// Connect to the database. Please change the password in the following line accordingly
	if (isset($_POST['register'])) {
	
		if ($_POST[Password] == $_POST[Password2]) {
			$db     = pg_connect("host=localhost port=5432 dbname=CS2102 user=postgres password=root");	
		
			$password = password_hash($_POST[Password],PASSWORD_DEFAULT);
			pg_send_query($db,"SELECT add_user('$_POST[Username]','$_POST[Email]','$password',
									'$_POST[Firstname]','$_POST[Lastname]','$_POST[dob]','$_POST[Gender]','False')");
								
			$result = pg_get_result($db);
			
			if ($result) {
				$state = pg_result_error_field($result,PGSQL_DIAG_SQLSTATE);
				
				if ($state == 0) {
					$_SESSION['user'] = $_POST[Username];
					$_SESSION['name'] = $_POST[Firstname] + $_POST[Lastname];
					$_SESSION["isAdmin"] = "False";
					header("Location: dashBoard.php");
					exit();
				} else if ($state == 23505) { //Unique Constraint
					$message = '<p>Email or Username is taken!</p>';
				} else if ($state == 23514) { //Restrict Constraint
					$message = '<p>You must be 18 and above to register!</p>';
				} else { //Catch all other failures
					$message = '<p>Some error encountered! Please contact the admin.</p>' + $state;
				}
				
			}
		} else {
			$message = '<p>Passwords does not match!</p>';
		}
	}
?>  
<!-- Registration Section !-->
<div class="w3-container w3-light-grey" style="padding:96px" id="home">
  <h3 class="w3-center">REGISTRATION</h3>
  <p class="w3-center w3-large">Register now to join the Task Sourcing Community!</p>
  <div class="w3-row-padding" style="margin-top:64px padding:128px 16px">
    <div class="w3-content" align="center">
      <form action="Registration.php" method="POST" >
        <p><input class="w3-input w3-border" type="text" placeholder="Username" required name="Username"></p>
		<p><input class="w3-input w3-border" type="password" placeholder="Password" required name="Password"></p>
		<p><input class="w3-input w3-border" type="password" placeholder="Verify Password" required name="Password2"></p>
		<p><input class="w3-input w3-border" type="text" placeholder="First Name" required name="Firstname"></p>
		<p><input class="w3-input w3-border" type="text" placeholder="Last Name" required name="Lastname"></p>
        <p><input class="w3-input w3-border" type="email" placeholder="Email" required name="Email"></p>
        <p><select class="w3-input w3-border" required name = "Gender">
			<option value = "Male"> Male </option>
			<option value = "Female"> Female </option>
			</select>
		</p>
		<p><input class="w3-input w3-border" type="date" required name="dob"></p>
        <p>
          <button class="w3-button w3-black" type="submit" name = "register">
            <i class="fa fa-sign-in"></i> REGISTER
          </button>
        </p>
      </form>
		<?php 
			echo $message;
		?>
    </div>
  </div>
</div>	
</body>
<?php
    include 'footer.html';
?>