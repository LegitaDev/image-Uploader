<?php
$servername = "localhost";
$us_sv = "";
$pw_sv = "";
$dbname = "";

// For Database class.
define("DBHOST", "localhost");
define("DBUSER", "");
define("DBPASS", "");
define("DBNAME", "");


$conn = mysqli_connect($servername, $us_sv, $pw_sv, $dbname);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
