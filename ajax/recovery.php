<?php
session_start();
include("../settings/connect_datebase.php");

$action = $_POST['action'] ?? '';
$login = $mysqli->real_escape_string($_POST['login'] ?? '');
$user_ip = $_SERVER['REMOTE_ADDR'];

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

    $query_user = $mysqli->query("SELECT id FROM `users` WHERE `login`='$login' AND `secret_answer`='$answer'");
    
    if ($user_data = $query_user->fetch_assoc()) {
        $user_id = $user_data['id'];

        $mysqli->query("DELETE FROM `ip_block` WHERE `ip` = '$user_ip'");
        $mysqli->query("UPDATE `users` SET `attempts` = 0 WHERE `id` = $user_id");

        if ($mode == 'unlock') {
            echo "success_unlock";
        } else {
            $new_pass = substr(md5(rand()), 0, 8); 
            $hashed = md5($new_pass); 
            $mysqli->query("UPDATE `users` SET `password`='$hashed' WHERE `id` = $user_id");
            echo "success_password";
        }
    } else {
        $mysqli->query("INSERT INTO `ip_block` (ip, attempts, last_attempt) VALUES ('$user_ip', 1, NOW()) 
                        ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
        echo "wrong_answer";
    }
    exit;
}
?>