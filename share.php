<!DOCTYPE html>
<html>

<head>
    <title>Shared Image</title>
    <?php
    include_once("./incl/header.php");
    ?>
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
    $imageID = $_GET["id"];
    $image = $img->getImageByID($imageID);
    if ($image == false) {
        $error = true;
        array_push($errorMessages, "No image has been found.");
    } else {
        $username = $user->getUsername($image->userID);
        $admin = $user->checkAdmin($image->userID);
    }

    if ($error == false) {
    }
}
?>

<body>
    <?php
    if (isset($_POST["likeBut"])) {
        $type = "likes";
    } elseif (isset($_POST["dislikeBut"])) {
        $type = "dislikes";
    }
    if (isset($type)) {
        if (!isset($_SESSION["id"])) {
            $error = true;
            array_push($errorMessages, "Login to be able to add reaction");
        } else {
            $checkReaction = $img->checkReactionExist($_SESSION["id"], $imageID);
            if ($checkReaction == false) {
                $addReaction = $img->addReaction($_SESSION["id"], $imageID, $type);
                $increaseReaction = $img->increaseReaction($imageID, $type);
            } elseif ($checkReaction !== $type) {
                $updateReaction = $img->updateReaction($_SESSION["id"], $imageID, $type);
                $editReactionValue = $img->editReactionValue($imageID, $type);
            } else {
                $removeReaction = $img->removeReaction($_SESSION["id"], $imageID, $type);
                $decreaseReaction = $img->decreaseReaction($imageID, $type);
            }
            header("Location: share.php?id=" . $imageID);
        }
    }
    // if (isset($_POST["likeBut"])) {
    //     $type = "like";
    //     $checkReaction = $img->checkReactionExist($_SESSION["id"], $imageID);
    //     if ($checkReaction == false) {
    //         $addLike = $img->addReaction($_SESSION["id"], $imageID, $type);
    //     } elseif ($checkReaction !== $type) {
    //         $updateLike = $img->updateReaction($_SESSION["id"], $imageID, $type);
    //     } else {
    //         $removeLike = $img->removeReaction($_SESSION["id"], $imageID, $type);
    //     }
    //     // $checkLike = $user->checkLikeDislike($_SESSION["id"], $imageID);
    //     // if ($checkLike == true) {
    //     //     $error = true;
    //     //     array_push($errorMessages, "You can't Like twice!");
    //     // } else {
    //     //     $liked = $img->addLike($imageID);
    //     //     if ($liked == true) {
    //     //         $addUserLike = $user->addUserLikeDislike($_SESSION["id"], $imageID);
    //     //     }
    //     // }
    // }
    // if (isset($_POST["dislikeBut"])) {
    //     $type = "dislike";
    //     $checkReaction = $img->checkReactionExist($_SESSION["id"], $imageID);
    //     if ($checkReaction == false) {
    //         $addLike = $img->addReaction($_SESSION["id"], $imageID, $type);
    //     } elseif ($checkReaction !== $type) {
    //         $updateLike = $img->updateReaction($_SESSION["id"], $imageID, $type);
    //     } else {
    //         $error = true;
    //         array_push($errorMessages, "You can't Dislike twice!");
    //     }

    //     // $checkLike = $user->checkLikeDislike($_SESSION["id"], $imageID);
    //     // if ($checkLike == true) {
    //     //     $error = true;
    //     //     array_push($errorMessages, "You can't Dislike twice!");
    //     // } else {
    //     //     $disliked = $img->addDislike($imageID);
    //     //     if ($disliked == true) {
    //     //         $addUserDislike = $user->addUserLikeDislike($_SESSION["id"], $imageID);
    //     //     }
    //     // }
    // }
    ?>

    <div class="container-lg">
        <?php include_once("./incl/nav.php"); ?>

        <h2><b><?php echo $username . "'s"; ?> image<b></h2>
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
                    <form action=\"\" method=\"POST\">
                    <img src=\"./uploads/" . $image->name . "\" style=\"border-radius:5px; padding:10px; max-width:900px;\"><br>
                    <button type=\"submit\" class=\"btn btn-outline-success\" name=\"likeBut\"><i class=\"fa-solid fa-thumbs-up\"></i> Like</button>
                    <a href=\"javascript:%20history.go(-1)&test\"><button class=\"btn btn-success\" type=\"button\">Go back</button></a>
                    <button type=\"submit\" class=\"btn btn-outline-danger\" name=\"dislikeBut\"><i class=\"fa-solid fa-thumbs-down\"></i> Dislike</button>
                    </form>
                    <br>
                    <span class=\"reactionsDesc\">" . $image->likes . " Likes&emsp;" . $image->dislikes . " Dislikes&emsp;</span>
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