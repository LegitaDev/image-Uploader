<?php

class Images
{
    private $database;
    public array $errors;
    public array $success;


    // Prepare database for use
    public function __construct()
    {
        $this->database = new Database;
        define("APPROOT", dirname(dirname(__FILE__)));
    }

    public function addImg($name, $userID)
    {
        $query = "INSERT INTO imageUploader (name,userID)";
        $query .= "VALUES (:name,:userID)";

        $this->database->prepare($query);
        $this->database->bind(":name", $name);
        $this->database->bind(":userID", $userID);
        $this->database->execute();
        return true;
    }

    public function delImg($name)
    {
        $path = APPROOT . "/../uploads/" . $name;
        if (unlink($path)) {
            return true;
        } else {
            return false;
        }
    }

    // Gets image by ID
    public function getImageByID($id)
    {
        $query = "SELECT * FROM imageUploader WHERE id=:id";
        $this->database->prepare($query);
        $this->database->bind(":id", $id);
        $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return $this->database->getRow();
        } else {
            return false;
        }
    }

    // Checks if the name already exists in the db
    public function nameExistCheck($name)
    {
        $query = "SELECT * FROM imageUploader WHERE name=:name";
        $this->database->prepare($query);
        $this->database->bind(":name", $name);
        $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


    // Checks if the name already exists in the db
    public function nameSearchCheck($name)
    {
        $query = "SELECT * FROM imageUploader WHERE name LIKE :name";
        $this->database->prepare($query);
        $this->database->bind(":name", "%" . $name . "%");
        $row = $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return $row->name;
        } else {
            return false;
        }
    }

    // Counts images
    public function imageCountTotal()
    {
        $query = "SELECT * FROM imageUploader";
        $this->database->prepare($query);
        $this->database->getRow();
        $counter = $this->database->rowCount();

        if ($counter > 0) {
            return $counter;
        } else {
            return false;
        }
    }

    public function imageCountUser($userID)
    {
        $query = "SELECT * FROM imageUploader WHERE userID=:userID";
        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $this->database->getRow();
        $counter = $this->database->rowCount();

        if ($counter > 0) {
            return $counter;
        } else {
            return false;
        }
    }

    public function deleteFileDB($deleteName, $userID)
    {
        //Query to check if the image asked to be deleted belongs to the same User ID

        $query = "SELECT * FROM imageUploader WHERE userID=:userID AND name LIKE :deleteName";
        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $this->database->bind(":deleteName", "%" . $deleteName . "%");
        $row = $this->database->getRow();

        if (!empty($row)) {
            $query = "DELETE FROM imageUploader WHERE userID=:userID AND name LIKE :deleteName";
            $this->database->prepare($query);
            $this->database->bind(":deleteName", "%" . $deleteName . "%");
            $this->database->bind(":userID", $userID);
            $this->database->execute();
            $this->success[] = "Image has been deleted.";
            return $row->name;
        } else {
            $this->errors[] = "Image not found.";
            return false;
        }
    }

    // Generate random name
    public function nameGenerator()
    {
        $str_result = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $codeLength = 10;
        $name = substr(str_shuffle($str_result), 0, $codeLength);

        return $name;
    }

    public function imageCount()
    {
        $query = "SELECT * FROM imageUploader";
        $this->database->prepare($query);
        $this->database->getRow();
        $counter = $this->database->rowCount();

        if ($counter > 0) {
            return $counter;
        } else {
            return false;
        }
    }

    // Returns last 3 uploaded images from user

    public function imageGallery($userID)
    {
        $query = "SELECT * FROM imageUploader WHERE userID=:userID ORDER BY date DESC LIMIT 15";
        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $array = $this->database->getArray();
        return $array;
    }

    // Returns the image age
    public function timeDifference($timestamp)
    {

        $datetime1 = new DateTime();
        $datetime2 = new DateTime($timestamp);
        $interval = $datetime1->diff($datetime2);

        $days = $interval->format('%d');
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');

        if ($days > 0) {
            return $days . " days and " . $hours . " hours ago";
        } else if ($hours == 1) {
            return $hours . " hour and " . $minutes . " minutes ago";
        } else if ($hours > 0) {
            return $hours . " hours and " . $minutes . " minutes ago";
        } else if ($minutes == 1) {
            return $minutes . " minute ago";
        } else if ($minutes > 0) {
            return $minutes . " minutes ago";
        } else if ($minutes == 0) {
            return "Less than one minute ago";
        } else {
            return "";
        }

        /*
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // var_dump($now . " ++ " . $ago . " ++ " . $diff);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
        */
    }

    // Returns all uploaded images from user
    public function userUploads($userID)
    {
        $query = "SELECT name FROM imageUploader WHERE userID=:userID ORDER BY date DESC";
        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $array = $this->database->getArray();
        return $array;
    }


    public function checkReactionExist($userID, $imageID)
    {
        $query = "SELECT * FROM imageUploaderReactions WHERE userID=:userID AND imageID=:imageID";
        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $this->database->bind(":imageID", $imageID);
        $row = $this->database->getRow();

        if ($this->database->rowCount() > 0) {
            return $row->type;
        } else {
            return false;
        }
    }

    public function addReaction($userID, $imageID, $type)
    {
        $query = "INSERT INTO imageUploaderReactions (userID,imageID,type)";
        $query .= "VALUES (:userID,:imageID,:type)";

        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $this->database->bind(":imageID", $imageID);
        $this->database->bind(":type", $type);
        $this->database->execute();
        return true;
    }

    public function updateReaction($userID, $imageID, $type)
    {
        $query = "UPDATE imageUploaderReactions SET type=:type WHERE userID=:userID AND imageID=:imageID";

        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $this->database->bind(":imageID", $imageID);
        $this->database->bind(":type", $type);
        $this->database->execute();
        return true;
    }

    public function removeReaction($userID, $imageID, $type)
    {
        $query = "DELETE FROM imageUploaderReactions WHERE userID=:userID AND imageID=:imageID AND type=:type";
        $this->database->prepare($query);
        $this->database->bind(":userID", $userID);
        $this->database->bind(":imageID", $imageID);
        $this->database->bind(":type", $type);
        $this->database->execute();
        return true;
    }

    public function increaseReaction($imageID, $type)
    {
        if ($type === "likes") {
            $query = "UPDATE imageUploader SET likes = likes + 1 WHERE id=:imageID";
        } elseif ($type === "dislikes") {
            $query = "UPDATE imageUploader SET dislikes = dislikes + 1 WHERE id=:imageID";
        } else {
            return false;
        }

        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->execute();
        return true;
    }

    public function decreaseReaction($imageID, $type)
    {
        if ($type === "likes") {
            $query = "UPDATE imageUploader SET likes = likes - 1 WHERE id=:imageID";
        } elseif ($type === "dislikes") {
            $query = "UPDATE imageUploader SET dislikes = dislikes - 1 WHERE id=:imageID";
        } else {
            return false;
        }

        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->execute();
        return true;
    }

    public function editReactionValue($imageID, $type)
    {
        if ($type === "likes") {
            $query = "UPDATE imageUploader SET likes = likes + 1 , dislikes = dislikes - 1 WHERE id=:imageID";
        } elseif ($type === "dislikes") {
            $query = "UPDATE imageUploader SET likes = likes - 1 , dislikes = dislikes + 1 WHERE id=:imageID";
        } else {
            return false;
        }
        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->execute();
        return true;
    }

    /*
    // Adds Like to the image
    public function addLike($imageID)
    {
        $query = "UPDATE imageUploader SET likes = likes + 1 WHERE id=:imageID";

        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->execute();
        return true;
    }

    // Adds Dislike to the image
    public function addDislike($imageID)
    {
        $query = "UPDATE imageUploader SET dislikes = dislikes + 1 WHERE id=:imageID";

        $this->database->prepare($query);
        $this->database->bind(":imageID", $imageID);
        $this->database->execute();
        return true;
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
