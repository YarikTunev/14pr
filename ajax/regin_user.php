<?php
session_start();
include("../settings/connect_datebase.php");

$user_ip = $_SERVER['REMOTE_ADDR'];
$current_time = time();

$ip_check = $mysqli->query("SELECT attempts, last_attempt FROM `ip_block` WHERE `ip`='$user_ip'");
$ip_data = $ip_check->fetch_assoc();

if ($ip_data && $ip_data['attempts'] >= 5) {
    $timeout = 30 * 60;
    if (time() - strtotime($ip_data['last_attempt']) < $timeout) {
        die("blocked_ip");
    } else {
        $mysqli->query("UPDATE `ip_block` SET `attempts` = 0 WHERE `ip` = '$user_ip'");
    }
}

if (!isset($_POST['login']) || !isset($_POST['password']) || !isset($_POST['question']) || !isset($_POST['answer'])) {
    die("Data missing");
}

$login = $mysqli->real_escape_string($_POST['login']);
$password = $_POST['password'];
$question = $mysqli->real_escape_string($_POST['question']);
$answer = $mysqli->real_escape_string(mb_strtolower(trim($_POST['answer'])));

$query_user = $mysqli->query("SELECT id FROM `users` WHERE `login`='$login'");

if($query_user && $query_user->num_rows > 0) {
    $mysqli->query("INSERT INTO `ip_block` (ip, attempts, last_attempt) 
                    VALUES ('$user_ip', 1, NOW()) 
                    ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
    echo "-1"; 
} else {
    $sql = "INSERT INTO `users`(`login`, `password`, `roll`, `attempts`, `secret_question`, `secret_answer`) 
            VALUES ('$login', '$password', 0, 0, '$question', '$answer')";
    
    if ($mysqli->query($sql)) {
        $id = $mysqli->insert_id;
        $mysqli->query("DELETE FROM `ip_block` WHERE `ip` = '$user_ip'");
        $_SESSION['user'] = $id;
        echo $id;
    } else {
        echo "SQL Error";
    }
}
?>