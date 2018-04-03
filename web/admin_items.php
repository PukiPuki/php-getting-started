<?php 
include 'pgconnect.php';
if (isset($_POST['add_item'])) {
    $query = "SELECT * FROM admin_add_items('$_POST[itemid]','$_POST[owner]', '$_POST[category]', '$_POST[itemname]', '$_POST[minbid]', '$_POST[autobuy]')";
    pg_send_query($pg_conn, $query);
    $result = pg_get_result($pg_conn);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);

        if ($state == 0) {
            echo "<script type='text/javascript'>
                alert('Item added!');
                          </script>";
                  } else if ($state == 23505) {
                      $message = "Item already exists";
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
        if (isset($_POST['edit_item'])) {
            $query = "SELECT * FROM admin_edit_items('$_POST[itemid]', '$_POST[category]', '$_POST[itemname]', '$_POST[minbid]', '$_POST[autobuy]', '$_POST[owner]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in editing item!');
            </script>";
        } else {
        echo "<script type='text/javascript'>
            alert('Item modified!');
        </script>";
        }
        }

        if (isset($_POST['delete_item'])) {
            $query = "SELECT * FROM admin_remove_items('$_POST[itemid]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                    if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in deleting item!');
            </script>";
        } else {
        echo "<script type='text/javascript'>
            alert('Item deleted!');
        </script>";

        }
        }
    header("Location: admin.php");

?>

