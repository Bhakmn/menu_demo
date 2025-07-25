<?php
session_start();

$valid_username = "admin";
$valid_password = "1234";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION["loggedin"] = true;
        header("Location: home.php");
        exit;
    } else {
        // Hatalı girişte error.html sayfasına yönlendirme
        header("Location: error.html");
        exit;
    }
}
?>
