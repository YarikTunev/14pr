<?php
session_start();
include("../settings/connect_datebase.php");

if (!isset($_SESSION['user'])) {
    exit("error_auth");
}

$current_time = time();
if (isset($_SESSION['last_msg_time']) && ($current_time - $_SESSION['last_msg_time']) < 5) {
    exit("flood");
}
$_SESSION['last_msg_time'] = $current_time;

$IdUser = (int)$_SESSION['user'];
$IdPost = (int)$_POST["IdPost"];

$Message = htmlspecialchars($_POST["Message"], ENT_QUOTES, 'UTF-8');
$Message = $mysqli->real_escape_string($Message);

if (!empty($Message)) {
    $mysqli->query("INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES ($IdUser, $IdPost, '$Message');");
    echo "success";
}
?>