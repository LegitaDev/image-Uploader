<div class="header">
    <div class="container">
        <a href="index.php" class="logo">Image Uploader</a>
        <ul class="main-nav">
            <?php
            if (!isset($_SESSION["id"])) {
                echo "<li><a href=\"login.php\">Login</a></li>";
                echo "<li><a href=\"index.php\">Main Page</a></li>";
            } else {
                echo "<li><a href=\"index.php?logout\">Logout</a></li>";
                echo "<li><a href=\"#\">" . $username . "</a></li>";
                if ($admin == 1) {
                    echo "<li><a href=\"adminPanel.php\">Admin Panel</a></li>";
                }
            }
            ?>
        </ul>
    </div>
</div>