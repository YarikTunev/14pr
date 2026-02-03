<?php
session_start();
include("../settings/connect_datebase.php");

if (isset($_SESSION['last_request_time']) && time() <= $_SESSION['last_request_time']) {
    exit("flood"); 
}
$_SESSION['last_request_time'] = time();

$login = $mysqli->real_escape_string($_POST['login']);
$password = $_POST['password'];

$user_check = $mysqli->query("SELECT id, attempts FROM `users` WHERE `login`='$login'");
$user_data = $user_check->fetch_assoc();

if ($user_data) {
    if ($user_data['attempts'] >= 5) {
        exit("blocked");
    }

    $query_user = $mysqli->query("SELECT id FROM `users` WHERE `login`='$login' AND `password`='$password'");
    $auth_data = $query_user->fetch_row();

    if ($auth_data) {
        $id = $auth_data[0];
        $mysqli->query("UPDATE `users` SET `attempts` = 0 WHERE `id` = $id");
        $_SESSION['user'] = $id;
        echo md5(md5($id));
    } else {
        $mysqli->query("UPDATE `users` SET `attempts` = `attempts` + 1 WHERE `login` = '$login'");
        echo "wrong"; 
    }
} else {
    echo "wrong";
}
?>