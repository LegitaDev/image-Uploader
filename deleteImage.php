<!DOCTYPE html>
<html>

<head>
    <title>Delete Image</title>
    <?php
    include_once("./incl/header.php");
    ?>
    <script>
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
        var delbutton = document.getElementById('delbutton')
        var popover = new bootstrap.Popover(delbutton, options)
    </script>
</head>

<?php
$error = false;
$errorMessages = [];
$success = false;
$successMessages = [];
$user = new Users();
$img = new Images();
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
}

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    $error = true;
    array_push($errorMessages, "No Image ID set");
} else {
    $id = $_GET["id"];
    $image = $img->getImageByID($id);
    if ($image == false) {
        $error = true;
        array_push($errorMessages, "No image has been found.");
    } else if ($image->userID !== $_SESSION["id"]) {
        $error = true;
        array_push($errorMessages, "You don't have the right premission for this action.");
    } else {
        $admin = $user->checkAdmin($image->userID);
    }

    if ($error == false) {
        if (isset($_GET["deleteConfirm"])) {
            if ($img->delImg($image->name) && $img->deleteFileDB($image->name, $_SESSION["id"])) {
                $success = true;
                array_push($successMessages, "Image has succesfully been deleted.");
                header("refresh:5;url=index.php");
            } else {
                $error = true;
                array_push($errorMessages, "An unkown error has occurred while deleting this image, please try again or contact an administrator.");
            }
        }
    }
}

?>

<body>

    <?php include_once("./incl/nav.php"); ?>

    <div class="container-lg">

        <h2>Delete image</h2>
        <div class="gallery" id="gallery">

            <center>
                <?php
                if ($success) {
                    echo "<div class=\"alert alert-success msgAlert\" role=\"alert\">";
                    foreach ($successMessages as $successMsg) {
                        echo $successMsg . "<br>";
                    }
                    echo "</div>";
                    exit;
                } else if ($error) {
                    echo "<div class=\"alert alert-danger msgAlert\" role=\"alert\">";
                    foreach ($errorMessages as $errorMsg) {
                        echo $errorMsg . "<br>";
                    }
                    echo "</div>";
                    exit;
                } else {
                    echo "
                    <img src=\"./uploads/" . $image->name . "\" style=\"border-radius:5px; padding:10px; max-width:900px;\"><br>
                    <a href=\"deleteImage.php?id=" . $image->id . "&deleteConfirm\"><button class=\"btn btn-danger\" type=\"button\">Delete Image</button></a>
                    <a href=\"javascript:%20history.go(-1)&test\"><button class=\"btn btn-success\" type=\"button\">Go back</button></a>   
                    ";
                }

                ?>

            </center>
        </div>
    </div>


    <?php
    include_once("./incl/footer.php");
    ?>
</body>

</html>