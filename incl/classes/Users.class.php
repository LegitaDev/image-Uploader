<?php

class Users
{
    private $database;
    public array $errors;
    public array $success;


    // Prepare database for use
    public function __construct()
    {
        $this->database = new Database;
    }

    public function login($username, $password)
    {
        if (empty($this->erros)) {
            // HASHED PASSWORD TESTING
            // $query = "SELECT * ";
            // $query .= "FROM imageUploaderUsers ";
            // $query .= "WHERE username = :username ";
            // $this->database->prepare($query);
            // $this->database->bind(":username", $username);
            // $row = $this->database->getRow();
            // if ($this->database->rowCount() > 0) {
            //     $passwordHashed = $row->password;
            //     if (password_verify($password, $passwordHashed)) {
            //         $_SESSION["id"] = $row->id;
            //         header("Location: index.php");
            //         return true;
            //     } else {
            //         $this->errors[] = "Username or Password is not correct";
            //         return false;
            //     }
            // }

            // Create and execute query
            $query = "SELECT * ";
            $query .= "FROM imageUploaderUsers ";
            $query .= "WHERE username = :username ";
            $query .= "AND password = :password";
            $this->database->prepare($query);
            $this->database->bind(":username", $username);
            $this->database->bind(":password", $password);
            $row = $this->database->getRow();
            if ($this->database->rowCount() > 0) {
                $_SESSION["id"] = $row->id;
                header("Location: index.php");
                return true;
            } else {
                $this->errors[] = "Username or Password is not correct";
                $this->loginAttempts($username);
                return false;
            }
        }
    }

    // Adds +1 to the loginAttempts in the DB for the username
    public function loginAttempts($username)
    {
        $query = "SELECT * ";
        $query .= "FROM imageUploaderUsers ";
        $query .= "WHERE username = :username";
        $this->database->prepare($query);
        $this->database->bind(":username", $username);
        $row = $this->database->getRow();
        //checks if username exists
        if ($this->database->rowCount() > 0) {
            $attempts = $row->loginAttempts;
            if ($attempts >= 5) {
                if (file_exists("loginAttempts.json")) {
                    $current_data = file_get_contents("loginAttempts.json");
                    $array_data = json_decode($current_data, true);

                    $IP = $_SERVER['REMOTE_ADDR'];
                    $time = time();
                    $content = array(
                        "username" => $username,
                        "IP" => $IP,
                        "time" => $time
                    );
                    $array_data[] = $content;
                    $final_data = json_encode($array_data);
                    file_put_contents("loginAttempts.json", $final_data);
                    // after this i want to add before anything in login, check the IP if it tried to login more than 5 times with false passwords to block it for some time
                } else {
                    $this->errors[] = "JSON file does not exist.";
                }
            } else {
                $q = "UPDATE imageUploaderUsers SET loginAttempts = loginAttempts + 1 WHERE username = :username";
                $this->database->prepare($q);
                $this->database->bind(":username", $username);
                $this->database->execute();
                return true;
            }
        } else {
            return false;
        }
    }

    public function getUsername($id)
    {
        $query = "SELECT * FROM imageUploaderUsers WHERE id=:id";
        $this->database->prepare($query);
        $this->database->bind(":id", $id);
        $row = $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return $row->username;
        } else {
            return false;
        }
    }

    public function getRegisterDate($id)
    {
        $query = "SELECT * FROM imageUploaderUsers WHERE id=:id";
        $this->database->prepare($query);
        $this->database->bind(":id", $id);
        $row = $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return $row->registerDate;
        } else {
            return false;
        }
    }

    public function addAccount($username, $password)
    {
        // Create and execute query
        $query = "SELECT username FROM imageUploaderUsers WHERE username=:username";
        $this->database->prepare($query);
        $this->database->bind(":username", $username);
        $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            $this->errors[] = "Username already exist";
            return false;
        }

        if (empty($this->errors)) {
            $query = "INSERT INTO imageUploaderUsers (username, password)";
            $query .= "VALUES (:username,:password)";

            $this->database->prepare($query);
            $this->database->bind(":username", $username);
            $this->database->bind(":password", $password);

            $this->database->execute();
            $this->success[] = "User has been added to the database.";

            return true;
        } else {
            return false;
        }
    }

    public function deleteAccount($username)
    {
        // Create and execute query
        $query = "SELECT username FROM imageUploaderUsers WHERE username=:username";
        $this->database->prepare($query);
        $this->database->bind(":username", $username);
        $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            $query = "DELETE FROM imageUploaderUsers WHERE username=:username";
            $this->database->prepare($query);
            $this->database->bind(":username", $username);
            $this->database->execute();

            $this->success[] = "User has been deleted.";
            return true;
        } else {
            $this->errors[] = "Username does not exist";
            return false;
        }
    }

    public function editUsername($oldUsername, $newUsername)
    {
        // Create and execute query
        $query = "SELECT username FROM imageUploaderUsers WHERE username=:oldUsername";
        $this->database->prepare($query);
        $this->database->bind(":oldUsername", $oldUsername);
        $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            $query = "UPDATE imageUploaderUsers SET username=:newUsername WHERE username=:oldUsername";
            $this->database->prepare($query);
            $this->database->bind(":oldUsername", $oldUsername);
            $this->database->bind(":newUsername", $newUsername);
            $this->database->execute();

            $this->success[] = "Username has been changed.";
            return true;
        } else {
            $this->errors[] = "Username does not exist";
            return false;
        }
    }

    public function searchUsername($username)
    {
        if (empty($this->erros)) {
            // Create and execute query
            $query = "SELECT username, registerDate ";
            $query .= "FROM imageUploaderUsers ";
            $query .= "WHERE username LIKE :name";
            $this->database->prepare($query);
            $this->database->bind(":name", "%" . $username . "%");

            $result = json_decode(json_encode($this->database->getArray()), true);
            if ($this->database->rowCount() > 0) {
                return $result;
            } else {
                $this->errors[] = "Username not found";
                return false;
            }
        }
    }

    public function userCount()
    {
        $query = "SELECT * FROM imageUploaderUsers";
        $this->database->prepare($query);
        $this->database->getRow();
        $counter = $this->database->rowCount();

        if ($counter > 0) {
            return $counter;
        } else {
            return false;
        }
    }

    public function userFound($username, $password)
    {
        $query = "SELECT * ";
        $query .= "FROM imageUploaderUsers ";
        $query .= "WHERE username = :username ";
        $query .= "AND password = :password";
        $this->database->prepare($query);
        $this->database->bind(":username", $username);
        $this->database->bind(":password", $password);
        $row = $this->database->getRow();
        if ($this->database->rowCount() > 0) {
            return $row->id;
        } else {
            $this->errors[] = "Username or Password is not correct";
            return false;
        }
    }

    public function addUserToImage($userID)
    {
        if (empty($this->errors)) {
            $query = "INSERT INTO imageUploader (userID)";
            $query .= "VALUES (:userID)";

            $this->database->prepare($query);
            $this->database->bind(":userID", $userID);

            $this->database->execute();
            return true;
        } else {
            return false;
        }
    }

    public function checkAdmin($id)
    {
        $query = "SELECT * FROM imageUploaderUsers WHERE id=:id";
        $this->database->prepare($query);
        $this->database->bind(":id", $id);
        $row = $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            $admin = $row->admin;
            if ($admin === "1") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    /*
    // Adds the image id to the user that liked or disliked
    public function addUserLikeDislike($userID, $imageID)
    {
        $query = "UPDATE imageUploaderUsers SET likesDislikes = likesDislikes + ', ' + :imageID WHERE id=:userID";
        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->bind(":userID", $userID);
        $this->database->execute();
        return true;
    }

    // Checks if user liked or disliked the following image
    public function checkLikeDislike($userID, $imageID)
    {
        $query = "SELECT * FROM imageUploaderUsers WHERE likesDislikes=:imageID AND id=:userID";
        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->bind(":userID", $userID);
        $row = $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
*/
    // Display Error and Success messages
    public function displayError()
    {
        if (count($this->errors) > 0) {
            $counter = count($this->errors);

            if ($counter == 0) {
                $result = "no errors";
            } else {
                $result = "<ul>";
                for ($i = 0; $i < $counter; $i++) {

                    $result .= "<div class=\"alertBoxBig\"><div class=\"alert alert-danger alertBox text-center\" role=\"alert\"><li>" . $this->errors[$i] . "</li></div></div>";
                }
                $result .= "</ul>";
            }
            return $result;
        }
    }
    public function displaySuccess()
    {
        if (count($this->success) > 0) {
            $counter = count($this->success);

            if ($counter == 0) {
                $result = "no success";
            } else {
                $result = "<ul>";
                for ($i = 0; $i < $counter; $i++) {
                    $result .= "<div class=\"alertBoxBig\"><div class=\"alert alert-success alertBox text-center\" role=\"alert\"><li>" . $this->success[$i] . "</li></div></div>";
                }
                $result .= "</ul>";
            }
            return $result;
        } else {
            return;
        }
    }
}
