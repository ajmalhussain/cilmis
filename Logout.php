<?php

session_start();

$role = $_SESSION['user_role'];
session_destroy();

header('Location:index.php');
?>