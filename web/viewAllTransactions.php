<!DOCTYPE html>
<?php
    session_start();
    $db = pg_connect("host=localhost port=5432 dbname=daq5hqoilto32t user=nnjquhqhcsmbzg password=ee407a056d0aa6ed4587a1aabee57672261bb4bc55addf7d78c018ca4dc133ee");
    $result = pg_query($db, "SELECT * FROM admin_select_transaction()");
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
</style>
</head>
<body>
<?php
    include 'navbar.php';
    function pg_connection_string_from_database_url() {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
    }
    if(!$result) {
        echo '<p>There is no task in the database!</p> </div> </div> </div>';
    } else {
        $index = 1;
        echo $db;
    }
?>

</body>
</html>