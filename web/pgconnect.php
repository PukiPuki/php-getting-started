<?php
    function pg_connection_string_from_database_url()
    {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
    }

    $pg_conn = pg_connect(pg_connection_string_from_database_url())
    or die('Could not connect:' . pg_last_error());
?>
