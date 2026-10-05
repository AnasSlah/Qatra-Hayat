<?php
session_start();
session_unset();
session_destroy();
// التوجيه إلى صفحة تسجيل الدخول الرئيسية (index.php) خارج مجلد user
header("Location: ../index.php"); 
exit();
?>