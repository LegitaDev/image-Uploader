<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel</title>
    <?php
    include_once("./incl/header.php");
    ?>
</head>
<?php
$user = new Users();

if (!isset($_SESSION["id"])) {
    header("Location: index.php?denied");
} else {
    $admin = $user->checkAdmin($_SESSION["id"]);
    if ($admin == false) {
        header("Location: index.php?denied");
    } else {
        $username = $user->getUsername($_SESSION["id"]);
    }
}
?>

<body>
    <?php include_once("./incl/nav.php"); ?>

    <?php

    $user = new Users();

    // Add Panel
    if (isset($_POST["addSubmit"])) {
        if (empty($_POST["username"]) || empty($_POST["password"])) {
            $user->errors[] = "Please fill up the inputs.";
        }

        if (empty($user->errors)) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $addAccount = $user->addAccount($username, $password);

            if ($addAccount) {
                echo $user->displaySuccess();
            } else {
                echo $user->displayError();
            }
        } else {
            echo $user->displayError();
        }
    }

    // Delete Panel
    if (isset($_POST["deleteSubmit"])) {
        if (empty($_POST["username"])) {
            $user->errors[] = "Please fill up the inputs.";
        }
        if (empty($user->errors)) {
            $username = $_POST["username"];

            $deleteAccount = $user->deleteAccount($username);
            if ($deleteAccount) {
                echo $user->displaySuccess();
            } else {
                echo $user->displayError();
            }
        } else {
            echo $user->displayError();
        }
    }

    // Edit Panel
    if (isset($_POST["editSubmit"])) {
        if (empty($_POST["oldUsername"]) || empty($_POST["newUsername"])) {
            $user->errors[] = "Please fill up the inputs.";
        }
        if (empty($user->errors)) {
            $oldUsername = $_POST["oldUsername"];
            $newUsername = $_POST["newUsername"];

            $editUsername = $user->editUsername($oldUsername, $newUsername);
            if ($editUsername) {
                echo $user->displaySuccess();
            } else {
                echo $user->displayError();
            }
        } else {
            echo $user->displayError();
        }
    }

    // Search for Username
    if (isset($_POST["searchSubmit"])) {
        if (empty($_POST["username"])) {
            $user->errors[] = "Please fill up the inputs.";
        }
        if (empty($user->errors)) {
            $username = $_POST["username"];

            $searchUsername = $user->searchUsername($username);
            if ($searchUsername) {
                $counter = count($searchUsername);
                for ($i = 0; $i < $counter; $i++) {
                    echo $searchUsername[$i]["username"] . " -- ";
                    echo $searchUsername[$i]["registerDate"] . "<br>";
                }
            } else {
                echo $user->displayError();
            }
        } else {
            echo $user->displayError();
        }
    }
    ?>

    <h4>Add Account</h4>
    <form action="adminPanel.php" method="POST">
        <div class="input-group mb-3">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <button name="addSubmit" type="submit" class="btn btn-outline-secondary" id="button-addon2">Add Account</button>
        </div>
    </form>

    <br><br><br>
    <h4>Delete Account</h4>
    <form action="adminPanel.php" method="POST">
        <div class="input-group mb-3">
            <input type="text" name="username" placeholder="Username">
            <button name="deleteSubmit" type="submit" class="btn btn-outline-secondary" id="button-addon2">Delete Account</button>
        </div>
    </form>

    <br><br><br>
    <h4>Edit Account</h4>
    <form action="adminPanel.php" method="POST">
        <div class="input-group mb-3">
            <input type="text" name="oldUsername" placeholder="Old Username">
            <input type="text" name="newUsername" placeholder="New Username">
            <button name="editSubmit" type="submit" class="btn btn-outline-secondary" id="button-addon2">Edit Account</button>
        </div>
    </form>

    <br><br><br>
    <h4>Search for Username</h4>
    <form action="adminPanel.php" method="POST">
        <div class="input-group mb-3">
            <input type="text" name="username" placeholder="Username">
            <button name="searchSubmit" type="submit" class="btn btn-outline-secondary" id="button-addon2">Search</button>
        </div>
    </form>

</body>

</html>