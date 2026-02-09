<?php
session_start();
include("../settings/connect_datebase.php");


$user_ip = $_SERVER['REMOTE_ADDR'];

$ip_check = $mysqli->query("SELECT attempts, last_attempt FROM `ip_block` WHERE `ip`='$user_ip'");
$ip_data = $ip_check->fetch_assoc();

if ($ip_data && $ip_data['attempts'] >= 10) {
    $timeout = 30 * 60; 
    if (time() - strtotime($ip_data['last_attempt']) < $timeout) {
        exit("blocked_ip");
    } else {
        // Время вышло, сбрасываем счетчик
        $mysqli->query("UPDATE `ip_block` SET `attempts` = 0 WHERE `ip` = '$user_ip'");
    }
}

$login = $mysqli->real_escape_string($_POST['login']);
$password = $_POST['password']; 


$user_check = $mysqli->query("SELECT id FROM `users` WHERE `login`='$login'");
$user_data = $user_check->fetch_assoc();

if ($user_data) {
    $query_user = $mysqli->query("SELECT id FROM `users` WHERE `login`='$login' AND `password`='$password'");
    $auth_data = $query_user->fetch_row();

    if ($auth_data) {
        $id = $auth_data[0];
        $mysqli->query("DELETE FROM `ip_block` WHERE `ip` = '$user_ip'");
        $_SESSION['user'] = $id;
        echo md5(md5($id));
    } else {
        $mysqli->query("INSERT INTO `ip_block` (ip, attempts, last_attempt) 
                        VALUES ('$user_ip', 1, NOW()) 
                        ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
        echo "wrong"; 
    }
} else {
    $mysqli->query("INSERT INTO `ip_block` (ip, attempts, last_attempt) 
                    VALUES ('$user_ip', 1, NOW()) 
                    ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
    echo "wrong";
}
?>