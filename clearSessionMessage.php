<?php
session_start();
unset($_SESSION['msg']);
if(isset($_POST['del_msg'])) 
    unset ($_POST['del_msg']);
?>

