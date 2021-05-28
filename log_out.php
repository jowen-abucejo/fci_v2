<?php
session_start();
session_destroy();
unset($_POST['username']);
unset($_POST['password']);
unset($_POST['utype']);
unset($_POST['fullname']);
$_SESSION['msg'] = null;
$_SESSION['unmame'] = null;
$_SESSION['utype'] = null;
$_SESSION['full_name'] = null;
header('location:log_in.php');
?>

