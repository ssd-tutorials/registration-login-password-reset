<?php
// *** Logout the current user.
$logoutGoTo = "login.php";
if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['MM_UserFName'] = NULL;
$_SESSION['MM_UserLName'] = NULL;
unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['MM_UserFName']);
unset($_SESSION['MM_UserLName']);
if ($logoutGoTo != "") {header("Location: $logoutGoTo");
exit;
}
?>
