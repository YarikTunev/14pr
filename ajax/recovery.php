<?php
    session_start();
    include("../settings/connect_datebase.php");

    $action = $_POST['action'] ?? '';
    $login = $mysqli->real_escape_string($_POST['login'] ?? '');
    
    function PasswordGeneration() {
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $max = 10;
        $size = strlen($chars) - 1;
        $password = "";
        while($max--) {
            $password .= $chars[rand(0, $size)];
        }
        return $password;
    }

    if ($action == "get_question") {
        $query = $mysqli->query("SELECT `secret_question` FROM `users` WHERE `login`='$login'");
        if ($query && $user = $query->fetch_assoc()) {
            echo $user['secret_question'];
        } else {
            echo "error_not_found";
        }
        exit;
    }

    if ($action == "verify") {
        $answer = $mysqli->real_escape_string(mb_strtolower(trim($_POST['answer'] ?? '')));
        $mode = $_POST['mode'] ?? 'password'; 

        $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='$login' AND `secret_answer`='$answer'");
        
        if($user_read = $query_user->fetch_row()) {
            $id = $user_read[0];

            if ($mode == 'unlock') {
                $mysqli->query("UPDATE `users` SET `attempts` = 0 WHERE `id` = $id");
                echo "success_unlock";
            } else {
                $password = PasswordGeneration();
                $hashed_password = md5($password);
                
                $mysqli->query("UPDATE `users` SET `password`='$hashed_password', `attempts` = 0 WHERE `id` = $id");
                echo "success_password"; 
            }
        } else {
            echo "wrong_answer";
        }
        exit;
    }

    echo "no_action";
?>