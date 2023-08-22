<!DOCTYPE html>
<html>

<head>
    <title>Upload your Image</title>
    <?php
    include_once("./incl/header.php");
    ?>
</head>

<?php
$user = new Users();
$img = new Images();


if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
}
if (isset($_GET["denied"])) {
    echo "<script>alert(\"You do not have access to that page\")</script>";
}

if (isset($_SESSION["id"])) {
    $userLogged = true;
    $username = $user->getUsername($_SESSION["id"]);
    $admin = $user->checkAdmin($_SESSION["id"]);
    $registerDate = $user->getRegisterDate($_SESSION["id"]);
    $imageCountUser = $img->imageCountUser($_SESSION["id"]);
    $imageGallery = $img->imageGallery($_SESSION["id"]);
    // echo "<pre>";
    // var_dump($imageGallery);
    // echo "</pre>";
} else {
    $userLogged = false;
}

?>

<body>

    <?php include_once("./incl/nav.php"); ?>

    <div class="landing">
        <div class="container">
            <div class="text">
                <h1>Welcome to Image Uploader</h1>
                <p>Upload your images, Gifs, Clips, save your memories and share them with your friends! </p>
            </div>
            <div class="image">
                <img src="assests/images/landing.png" alt="">
            </div>
        </div>
        <?php
        if (!isset($_SESSION["id"])) {
            echo "<a href=\"login.php\" class=\"i-login\">
            <i class=\"fa-solid fa-right-to-bracket\"> Login</i>
        </a>";
        }
        ?>
    </div>

    <?php

    $imageCountTotal = $img->imageCountTotal();

    if (isset($_POST["showSubmit"])) {
        if (empty($_POST["showName"])) {
            $img->errors[] = "Please fill up the inputs.";
        }

        if (empty($img->errors)) {
            $inputName = $_POST["showName"];
            if (substr($inputName, 0, 6) === "image-") {
                $fileNameNew = $inputName . ".";
            } else {
                $fileNameNew = "image-" . $inputName . ".";
            }

            $search = $img->nameSearchCheck($fileNameNew);
            if ($search) {
                header("Location: uploads/" . $search);
            } else {
                echo "Image not found";
            }
        } else {
            echo $img->displayError();
        }
    }

    if (isset($_POST["deleteSubmit"])) {
        $deleteName = $_POST["deleteName"];
        $id = $_SESSION["id"];

        if (empty($deleteName)) {
            $img->errors[] = "Please fill up the inputs.";
        }
        if (strlen($deleteName) < 10) {
            $img->errors[] = "Please make sure the image name is 10 characters";
        }

        if (empty($img->errors)) {
            if (substr($deleteName, 0, 6) === "image-") {
                $fileNameNew = $deleteName . ".";
            } else {
                $fileNameNew = "image-" . $deleteName . ".";
            }

            $search = $img->nameSearchCheck($fileNameNew);

            if ($search) {
                $deleteFileDB = $img->deleteFileDB($fileNameNew, $id);

                if ($deleteFileDB == true) {
                    $path = "uploads/" . $deleteFileDB;
                    if (!unlink($path)) {
                        echo "You have an error.";
                    } else {
                        echo $img->displaySuccess();
                    }
                } else {
                    echo $img->displayError();
                }
            } else {
                $img->errors[] = "Image Not found";
                echo $img->displayError();
            }
        } else {
            echo $img->displayError();
        }
    }

    // To get total registered users number
    $userCount = $user->userCount();
    $imgCounter = $imageCountTotal;

    // To check server status
    if ($userLogged === true) {
        $imgCounterUser = $imageCountUser;
        $colSize = 4;
    } else {
        $colSize = 6;
    }

    ?>
    <div class="container">
        <center>
            <h2 class="main-title">Our Status</h2>
            <div class="containerCounter">
                <div class="row">
                    <div class="four col-md-<?php echo $colSize ?>">
                        <div class="counter-boxCounter counterColored"> <i class="fa-solid fa-user-group"></i> <span class="counter"><?php echo $userCount; ?></span>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="four col-md-<?php echo $colSize ?>">
                        <div class="counter-boxCounter"> <i class="fa-solid fa-file-arrow-up"></i> <span class="counter"><?php echo $imgCounter; ?></span>
                            <p>Uploaded Images</p>
                        </div>
                    </div>

                    <?php if ($userLogged) : ?>
                        <div class="four col-md-<?php echo $colSize ?>">
                            <div class="counter-boxCounter"> <i class="fa-solid fa-file-arrow-up"></i> <span class="counter"><?php echo $imgCounterUser; ?></span>
                                <p>Your Images</p>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>
        </center>
    </div>

    <?php
    if (isset($_SESSION["id"])) : ?>
        <div class="container-lg">
            <center>
                <!-- Upload File -->
                <div class="container">
                    <!-- Modal -->
                    <div id="uploadModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">File upload form</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form -->
                                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                                        Select file : <input type='file' name='fileToUpload' id='fileToUpload' class='form-control'><br>
                                        <input name="submit" type='submit' class='btn btn-info' value='Upload'>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="glow-on-hover" data-toggle="modal" data-target="#uploadModal">Upload File</button>
                </div>
            </center>

            <br>
            <h2>Lates uploaded images</h2>
            <div class="gallery" id="gallery">

                <div class="container">
                    <?php

                    foreach ($imageGallery as $image) {

                        echo "
                        <div class=\"card box\" style=\"width: 18rem;\">
                            <div class=\"image\">
                                <img src=\"uploads/" . $image->name . "\" class=\"card-img-top\" alt=\"...\">
                            </div>
                            <div class=\"card-body\">

                            </div>
                            <div class=\"card-header\">
                                Uploaded " . $img->timeDifference($image->date) . "
                            </div>

                            <a href=\"share.php?id=" . $image->id . "\"><button class=\"btn btn-success\">Share</button></a>
                            <a href=\"deleteImage.php?id=" . $image->id . "\"><button class=\"btn btn-danger\">Delete</button></a>
                        </div>

                        ";
                    }

                    ?>
                </div>
            </div>
        </div>
    <? endif; ?>

    <?php
    include_once("./incl/footer.php");
    ?>
</body>

</html>