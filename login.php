<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>
    <?php
    include_once("./incl/header.php");
    ?>
</head>
<?php

if (isset($_SESSION["id"])) {
    header("Location: index.php?logged");
}
?>

<body>
    <?php include_once("./incl/nav.php"); ?>

    <?php
    $user = new Users();

    if (isset($_POST["submit"])) {
        if (empty($_POST["username"]) || empty($_POST["password"])) {
            $user->errors[] = "Please fill up the inputs.";
        }

        if (empty($user->errors)) {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $login = $user->login($username, $password);
            if ($login == false) {
                echo $user->displayError();
            } else {
                header("Location: index.php");
            }
        } else {
            echo $user->displayError();
        }
    }

    ?>

    <form action="login.php" method="POST" class="form">
        <h2>Login</h2>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
    </form>

</body>

</html>