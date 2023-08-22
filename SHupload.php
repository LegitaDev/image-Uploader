<?php

include_once("./incl/conn.php");

spl_autoload_register(function ($class) {
    require_once("./incl/classes/" . $class . ".class.php");
});

$permsError = false;
$sharexErrors = false;
$user = new Users();
$img = new Images();

if (isset($_GET["username"]) && !empty($_GET["username"]) && isset($_GET["password"]) && !empty($_GET["password"])) {
    $username = $_GET["username"];
    $password = $_GET["password"];

    $userID = $user->userFound($username, $password);

    if ($userID) {
        // for futher 
    } else {
        $permsError = true;
    }
} else {
    echo "Invalid Parameters given";
    exit;
}

if ($permsError) {
    echo "No permission.";
    exit;
}

if (isset($_FILES["sharex"])) {

    $file = $_FILES["sharex"];

    $fileName = $file["name"];
    $fileTmpName = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileError = $file["error"];
    $fileType = $file["type"];
} else {
    echo "Error defining file.";
    exit;
}





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
        $img->errors[] = "Your file is too big! Maximum size is 5GB";
    }

    if (empty($img->errors)) {

        do {
            $newName = time();
            $fileNameNew = "image-" . $newName . "." . $fileActualExt;
        } while ($img->nameExistCheck($fileNameNew));

        if ($img->nameExistCheck($fileNameNew)) { // Just in case
            $img->errors[] = "Name already exists";
        }

        if (empty($img->errors)) {
            $fileDestination = "uploads/" . $fileNameNew;
            move_uploaded_file($fileTmpName, $fileDestination);

            $addImg = $img->addImg($fileNameNew, $userID);
        } else {
            echo $img->displayError();
        }
    }
} else {
    $img->errors[] = "There was an Error uploading your file";
    echo $img->displayError();
}

if (empty($img->errors)) {
    echo "" . $fileNameNew; // Add link
} else {
    echo "Unexpect error.";
    exit;
}
