<?php require_once('Connections/conndb.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['frm_username'])) {
  $loginUsername=$_POST['frm_username'];
  $password=md5($_POST['frm_password']);
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "login_successful.php";
  $MM_redirectLoginFailed = "login_failed.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_conndb, $conndb);
  
  $LoginRS__query=sprintf("SELECT usr_email, usr_password, usr_f_name, usr_l_name FROM tbl_users WHERE usr_email=%s AND usr_password=%s AND usr_active=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text"), GetSQLValueString('y', "text"));
   
  $LoginRS = mysql_query($LoginRS__query, $conndb) or die(mysql_error());
  $row_LoginRS = mysql_fetch_assoc($LoginRS);
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
	$_SESSION['MM_UserFName'] = $row_LoginRS['usr_f_name'];
	$_SESSION['MM_UserLName'] = $row_LoginRS['usr_l_name'];

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="styles/core.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form id="frm_insert" name="frm_insert" method="POST" action="<?php echo $loginFormAction; ?>">
  <table border="0" cellspacing="0" cellpadding="0" id="tbl_insert">
    <tr>
      <th scope="row"><label for="frm_username">Username:</label></th>
      <td><input name="frm_username" type="text" class="frm_fld" id="frm_username" /></td>
    </tr>
    <tr>
      <th scope="row"><label for="frm_password">Password:</label></th>
      <td><input name="frm_password" type="password" class="frm_fld" id="frm_password" /></td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td><label>
          <input type="submit" name="button" id="button" value="Login" />
        </label></td>
    </tr>
  </table>
</form>
<p><a href="reset_password.php">Forgot password?</a></p>
</body>
</html>