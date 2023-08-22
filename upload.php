<?php
include_once("./incl/conn.php");

spl_autoload_register(function ($class) {
    require_once("./incl/classes/" . $class . ".class.php");
});

session_start();

$img = new Images();
$id = $_SESSION["id"];
$file = $_FILES["fileToUpload"];

$fileName = $_FILES["fileToUpload"]["name"];
$fileTmpName = $_FILES["fileToUpload"]["tmp_name"];
$fileSize = $_FILES["fileToUpload"]["size"];
$fileError = $_FILES["fileToUpload"]["error"];
$fileType = $_FILES["fileToUpload"]["type"];

$fileExt = explode(".", $fileName); // To get the file extension like png,jpg,gif etc
$fileActualExt = strtolower(end($fileExt)); // To get the last part of the file name (png,etc)

$allowed = array("jpg", "jpeg", "png", "gif");

if ($fileError === 0) {

    // Checks if the uploaded file from the allowed types
    if (!in_array($fileActualExt, $allowed)) {
        $img->errors[] = "Only \"jpg , jpeg , png , gif\" types are allowed to be uploaded";
    }

    // Checks the uploaded file size
    if ($fileSize > 5e+6) { // 5e+6 = 5Mb
        $img->errors[] = "Your file is too big! Maximum size is 5Mb";
    }

    if (empty($img->errors)) {

        do {
            $newName = time();
            $fileNameNew = "image-" . $newName . "." . $fileActualExt;
        } while ($img->nameExistCheck($fileNameNew));

        if ($img->nameExistCheck($fileNameNew)) { // Just in case
            $img->errors[] = "Name already exists";
        }
    }

    if (empty($img->errors)) {
        $fileDestination = "uploads/" . $fileNameNew;
        move_uploaded_file($fileTmpName, $fileDestination);

        $addImg = $img->addImg($fileNameNew, $id);
    } else {
        echo $img->displayError();
    }
} else {
    $img->errors[] = "There was an Error uploading your file";
    echo $img->displayError() . "<br>";
    echo  "<button type=\"button\"><a href=\"index.php\">Main Page</a></button>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Image upload status</title>
    <style>
        body {
            background-color: gray;
        }
    </style>
</head>

<body>
    <?php

    if (empty($img->errors)) {
        echo "<center>";
        echo "<h2>Your image has been uploaded!</h2>";
        echo "<img src=\"uploads/" . $fileNameNew . "\">";
        echo "</center>";
        echo "<button><a href=\"index.php\">Go Back</a></button>";
    }


    ?>



</body>

</html>