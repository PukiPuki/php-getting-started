<?php 
include 'pgconnect.php';
include 'checkadmin.php';
if (isset($_POST['add_bid'])) {
    $query = "SELECT * FROM admin_add_bids('$_POST[bidstatus]', '$_POST[bidprice]', '$_POST[biddername]', '$_POST[tid]')";
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
            $result = pg_query($pg_conn, $query); 
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
            $result = pg_query($pg_conn, $query);
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
    header("Location: admin.php");

?>

