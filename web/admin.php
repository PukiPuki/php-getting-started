<!DOCTYPE html>
<?php
session_start();
include 'pgconnect.php';
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        form {
            border: 3px solid #f1f1f1;
        }

        /* Full-width inputs */
        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Set a style for all buttons */
        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        /* Add a hover effect for buttons */
        button:hover {
            opacity: 0.8;
        }

        /* Extra style for the cancel button (red) */
        .cancelbtn {
            width: auto;
            padding: 10px 18px;
            background-color: #f44336;
        }

        /* Center the avatar image inside this container */
        .imgcontainer {
            text-align: center;
            margin: 24px 0 12px 0;
        }

        /* Avatar image */
        img.avatar {
            width: 40%;
            border-radius: 50%;
        }

        /* Add padding to containers */
        .container {
            padding: 16px;
        }

        /* The "Forgot password" text */
        span.psw {
            float: right;
            padding-top: 16px;
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.psw {
                display: block;
                float: none;
            }

            .cancelbtn {
                width: 100%;
            }
        }

    </style>
</head>
<body>
<?php
include 'navbar.php';

if (!$_SESSION[isAdmin]) {
    header('Location:index.php');
} else {

}

#TODO: Add checking of valid username, etc
if (isset($_POST['add_user'])) {
    $password = password_hash($_POST[password], PASSWORD_DEFAULT);
    $query = "SELECT * FROM admin_add_user('$_POST[username]', '$password', '$_POST[phone]', '$_POST[isAdmin]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

        if ($state == 0) {
            echo "<script type='text/javascript'>
                    alert('User added!');
                    </script>";
        } else if ($state == 23505) {
            $message = "User already exists";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else if ($state == 23502) {
            $message = "You have somehow entered a null value!";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else
            echo $state;
    } else {
        echo "<script>
              alert('Update failed!');
            </script>";
    }
} else if (isset($_POST['edit_user'])) {
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $query = "SELECT * FROM admin_edit_user('$_POST[username]', '$_POST[newphone]', '$_POST[isAdmin]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

        if ($state == 0) {
            echo "<script type='text/javascript'>
                    alert('User edited!');
                    </script>";
        } else if ($state == 23505) {
            $message = "No user found";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else if ($state == 23502) {
            $message = "You have somehow entered a null value!";
            echo "<script type='text/javascript'>
                    alert('$message');
                    </script>";
        } else
            echo "Error Code: " . $state;
    } else {
        echo "<script>
              alert('Update failed!');
            </script>";
    }
} else if (isset($_POST['remove_user'])) {
    $pg_conn = pg_connect(pg_connection_string_from_database_url());
    $query = "SELECT * FROM admin_remove_user('$_POST[username]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        echo "<script type='text/javascript'>
              alert('User removed!');
            </script>";
    } else {
        echo "<script>
              alert('User removal failed!');
            </script>";
    }
} else {

}
?>

<div style="margin-top:43px">
    <div>
        <h1 class="w3-text-teal"> User Control </h1>

        <div class="container">
            <h2 class="w3-text-teal"> Add User </h2>
            <form action="admin.php" method="POST">
                <div class="container">
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <label for="password"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <label for="phone"><b>Phone Number</b></label>
                    <input type="text" placeholder="Enter Phone Number" name="phone" required>
                    <label for="isAdmin"><b>Admin Privileges</b></label>
                    <input type="hidden" name="isAdmin" value="0"> </input>
                    <input type="checkbox" name="isAdmin" value="1"> Admin </input>
                    <button type="submit" name="add_user">Add User</button>
                </div>
            </form>
        </div>

        <form action="admin.php" method="POST">
            <div class="container">
                <h2 class="w3-text-teal"> Edit User </h2>
                <label for="username"><b>Username</b></label>
                <input type="text" placeholder="Enter User to Edit" name="username" required>

                <label for="newphone"><b>New Phone Number</b></label>
                <input type="text" placeholder="Enter New Phone Number" name="newphone" required>

                <label for="isAdmin"><b>Admin Privileges</b></label>
                <input type="hidden" name="isAdmin" value="0"> </input>
                <input type="checkbox" name="isAdmin" value="1"> Admin </input>
                <button type="submit" name="edit_user">Edit User</button>
            </div>
        </form>

        <div class="container">
            <h2 class="w3-text-teal"> Remove User </h2>
            <form action="admin.php" method="POST">
                <div class="container">
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter User to Remove" name="username" required>
                    <button type="submit" name="remove_user">Remove User</button>
                </div>
            </form>
        </div>
    </div>
<!-- For bids--> 
    <div>
        <h1 class="w3-text-teal"> Bids </h1>
     
        <div class="container">
    
            <div>
            <?php 
                $query = "SELECT * FROM bids";
               $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                $index = 1;
                echo '
                <table class="striped responsive-table centered highlight", style="width:100%">
                        <tr>
                        <th>S/N</th>
                        <th>tID</th>
                        <th>Bidder Name</th>
                        <th>Bid Price</th>
                        <th>Bidding status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        </tr>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr align = "center">
                    <form action="admin.php" method="POST">
                    <input type="hidden" name="tid" value="'.$row["tid"].'"/>
                    <input type="hidden" name="biddername" value="'.$row["biddername"].'"/>
                    <td>'.$index.'</td>
                    <td>'.$row["tid"].'</td>
                    <td>'.$row["biddername"].'</td>
                    <td><input type="text" name="bidprice" value="'.$row["biddingprice"].'"/></td>
                    <td><input type="text" name="bidstatus" value="'.$row["biddingstatus"].'"/></td>
                    <td><button type="submit" name="edit_bid" >Edit</button></td>
                    <td><button type="submit" name="delete_bid" >Delete</button></td>
                    </form>
                    </tr>';
                    $index++;
            }

?>

            </div>
            
            <h2 class="w3-text-teal"> Add Bid </h2>
            <form action="admin.php" method="POST">
                <div class="container">
                    <label for="bidstatus"><b>Bid Status</b></label>
                    <input type="text" placeholder="Status" name="bidstatus" required>
                    <label for="bidprice"><b>Bid Price</b></label>
                    <input type="text" placeholder="Price" name="bidprice" required>
                    <label for="biddername"><b>Bidder Name</b></label>
                    <input type="text" placeholder="Bidder Name" name="biddername" required>
                    <button type="submit" name="add_bid">Add Bid</button>
                </div>
            </form>
        </div>
        <?php 
            if (isset($_POST['add_bid'])) {
                  $query = "SELECT * FROM admin_add_bids('$_POST[bidstatus]', '$_POST[bidprice]', '$_POST[biddername]')";
                  pg_send_query($pg_conn, $query);
                  $result = pg_get_result($pg_conn);

                  if ($result) {
                      $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

                  if ($state == 0) {
                      echo "<script type='text/javascript'>
                          alert('Bid added!');
                          </script>";
                  } else if ($state == 23505) {
                      $message = "Bid already exists";
                      echo "<script type='text/javascript'>
                          alert('$message');
                          </script>";
                  } else if ($state == 23502) {
                      $message = "You have somehow entered a null value!";
                      echo "<script type='text/javascript'>
                          alert('$message');
                          </script>";
                  } else
                      echo "<script type='text/javascript'>
                            alert('$state');
                          </script>";
          } else {
              echo "<script>
                    alert('Update failed!');
                  </script>";
          }
        } 
        if (isset($_POST['edit_bid'])) {
            $query = "SELECT * FROM admin_edit_bids('$_POST[tid]','$_POST[bidstatus]','$_POST[bidprice]','$_POST[biddername]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in editing bid!');
            </script>";
        } else {
        echo "<script type='text/javascript'>
                alert('Bid modified!');
            </script>";

        }
        }

        if (isset($_POST['delete_bid'])) {
            $query = "SELECT * FROM admin_remove_bids('$_POST[tid]','$_POST[bidstatus]','$_POST[bidprice]','$_POST[biddername]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                    if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in deleting bid!');
            </script>";
        } else {
        echo "<script type='text/javascript'>
                alert('Bid deleted!');
            </script>";

        }
        }

        ?>

        </div>
</div>
</body>
</html>
