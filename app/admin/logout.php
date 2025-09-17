<?php
/**
 * BuildTech CMS - Admin Logout
 * Admin çıkış işlemi
 */

session_start();
session_destroy();
header('Location: login.php');
exit;
?>
