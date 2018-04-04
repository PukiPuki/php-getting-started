<?php 
include 'pgconnect.php';
include 'checkadmin.php';
if (isset($_POST['add_transaction'])) {
    $query = "SELECT * FROM admin_add_transaction('$_POST[location]', '$_POST[pickupdate]', '$_POST[returndate]', '$_POST[itemid]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

        if ($state == 0) {
            echo "<script type='text/javascript'>
                alert('Transaction added!');
                          </script>";
                  } else if ($state == 23505) {
                      $message = "Transaction already exists";
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
        if (isset($_POST['edit_transaction'])) {
            $query = "SELECT * FROM admin_edit_transaction('$_POST[tid]','$_POST[location]','$_POST[pickupdate]','$_POST[returndate]', '$_POST[itemid]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in editing transaction!');
            </script>";
        } else {
        echo "<script type='text/javascript'>
            alert('Transaction modified!');
        </script>";
        }
        }

        if (isset($_POST['delete_transaction'])) {
            $query = "SELECT * FROM admin_remove_transaction('$_POST[tid]', '$_POST[location]','$_POST[pickupdate]','$_POST[returndate]','$_POST[itemid]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                    if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in deleting transaction!');
            </script>";
        } else {
        echo "<script type='text/javascript'>
            alert('Transaction deleted!');
        </script>";

        }
        }
    header("Location: admin.php");

?>

