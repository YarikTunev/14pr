<?php
session_start();
include("../settings/connect_datebase.php");

$current_time = time();
if (isset($_SESSION['last_reg_time']) && ($current_time - $_SESSION['last_reg_time']) < 15) {
    die("-2"); 
}
$_SESSION['last_reg_time'] = $current_time;

if (!isset($_POST['login']) || !isset($_POST['password']) || !isset($_POST['question']) || !isset($_POST['answer'])) {
    die("Data missing");
}

$login = $mysqli->real_escape_string($_POST['login']);
$password = $_POST['password'];
$question = $mysqli->real_escape_string($_POST['question']);
$answer = $mysqli->real_escape_string(mb_strtolower(trim($_POST['answer'])));

$query_user = $mysqli->query("SELECT id FROM `users` WHERE `login`='$login'");

if($query_user && $query_user->num_rows > 0) {
    echo "-1"; 
} else {

    $sql = "INSERT INTO `users`(`login`, `password`, `roll`, `attempts`, `secret_question`, `secret_answer`) 
            VALUES ('$login', '$password', 0, 0, '$question', '$answer')";
    
    if ($mysqli->query($sql)) {
        $id = $mysqli->insert_id;
        $_SESSION['user'] = $id;
        echo $id;
    } else {
        echo "SQL Error: " . $mysqli->error;
    }
}
?>