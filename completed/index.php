<?php require_once('Connections/conndb.php'); ?>
<?php include('inc/functions.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_insert")) {
	
	$fld_required = array('vis_f_name','vis_l_name','vis_email','vis_password');
	$fld_missing = array();
	
	foreach($_POST as $key => $value) {
		
		if (empty($value) && in_array($key, $fld_required)) {
			
			array_push($fld_missing, $key);
			
		}
		
	}
	
	if ($_POST['vis_email'] != '') {
		
		if (!validEmail($_POST['vis_email'])) {
			
			array_push($fld_missing, 'email_invalid');
			
		}
		
	}
	
	mysql_select_db($database_conndb, $conndb);
	$query_rsDuplicate = "SELECT usr_email FROM tbl_users WHERE usr_email = '".$_POST['vis_email']."'";
	$rsDuplicate = mysql_query($query_rsDuplicate, $conndb) or die(mysql_error());
	$row_rsDuplicate = mysql_fetch_assoc($rsDuplicate);
	$totalRows_rsDuplicate = mysql_num_rows($rsDuplicate);
	
	if ($totalRows_rsDuplicate > 0) {
		
		array_push($fld_missing, 'duplicate');
		
	}
	
	mysql_free_result($rsDuplicate);
	
	if (!empty($fld_missing)) {
		
		if (in_array('vis_f_name', $fld_missing)) {
		
			$vis_f_name = 'Please provide your first name';
			
		}
		
		if (in_array('vis_l_name', $fld_missing)) {
		
			$vis_l_name = 'Please provide your last name';
			
		}
		
		if (in_array('vis_email', $fld_missing) || in_array('email_invalid', $fld_missing)) {
		
			$vis_email = 'Please provide your valid email address';
			
		}
		
		if (in_array('duplicate', $fld_missing)) {
		
			$vis_email = 'Email address already registered';
			
		}
		
		if (in_array('vis_password', $fld_missing)) {
		
			$vis_password = 'Please create your password';
			
		}		
		
	} else {
	
	  $ins_date = date('Y-m-d H:i:s');
	  $vis_password_md5 = md5($_POST['vis_password']);
		
	  $insertSQL = sprintf("INSERT INTO tbl_users (usr_f_name, usr_l_name, usr_email, usr_password, usr_date) VALUES (%s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['vis_f_name'], "text"),
						   GetSQLValueString($_POST['vis_l_name'], "text"),
						   GetSQLValueString($_POST['vis_email'], "text"),
						   GetSQLValueString($vis_password_md5, "text"),
						   GetSQLValueString($ins_date, "text"));
	  
	
	  mysql_select_db($database_conndb, $conndb);
	  $Result1 = mysql_query($insertSQL, $conndb) or die(mysql_error());
	  
	  mysql_select_db($database_conndb, $conndb);
	$query_rsFindID = "SELECT usr_id FROM tbl_users WHERE usr_email = '".$_POST['vis_email']."'";
	$rsFindID = mysql_query($query_rsFindID, $conndb) or die(mysql_error());
	$row_rsFindID = mysql_fetch_assoc($rsFindID);
	$totalRows_rsFindID = mysql_num_rows($rsFindID);
	
	$usr_id = $row_rsFindID['usr_id'];
	$hash = mt_rand().$usr_id.mt_rand();
	
	mysql_free_result($rsFindID);
	
	$updateSQL = sprintf("UPDATE tbl_users SET usr_hash = '".$hash."' WHERE usr_id = $usr_id");
	  	
	  mysql_select_db($database_conndb, $conndb);
	  $Result2 = mysql_query($updateSQL, $conndb) or die(mysql_error());
	 
	 if ($Result2) {
		 
		$to = $_POST['vis_email'];
		$subject = 'Activate your account';
		$from = 'dummy@emailaddress.co.uk';
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";
		$headers .= "From: My Website Name <".$from.">\r\n";
		$headers .= "Reply-to: My Website Name <".$from.">\r\n";
		
		$message = '<div style="font-family:Arial, Verdana, Sans-serif; color:#333; font-size:12px">
		<p>Thank you for registering on our website</p>
		<p>Please click on the following link to activate your account: <a href="http://localhost:8888/OTHER/TUTORIALS/reg_log/activate.php?id='.$hash.'">http://localhost:8888/OTHER/TUTORIALS/reg_log/activate.php?id='.$hash.'</a></p></div>';
		
		if (mail($to, $subject, $message, $headers)) {
			
			$success = '<p>Thank you.<br />You have successfully registered.</p>';
			
		} else {
			
			$success = '<p>Error.<br />Your activation link could not be sent.<br />Please contact administrator.</p>';
			
		}
		
				 
	 }
	   
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
<?php if (isset($success)) { echo $success; } else { ?>
<form id="frm_insert" name="frm_insert" method="POST" action="<?php echo $editFormAction; ?>">
  <table border="0" cellpadding="0" cellspacing="0" id="tbl_insert">
    <?php if (isset($vis_f_name)) { ?>
      <tr>
        <th>&nbsp;</th>
        <td class="frm_warn"><?php echo $vis_f_name; ?></td>
      </tr>
      <?php } ?>
    <tr>
      <th scope="row"><label for="vis_f_name">First name:</label></th>
      <td><input name="vis_f_name" type="text" class="frm_fld" id="vis_f_name"<?php getStickyInsert('vis_f_name'); ?> /></td>
    </tr>
    <?php if (isset($vis_l_name)) { ?>
      <tr>
        <th>&nbsp;</th>
        <td class="frm_warn"><?php echo $vis_l_name; ?></td>
      </tr>
      <?php } ?>
    <tr>
      <th scope="row"><label for="vis_l_name">Last name:</label></th>
      <td><input name="vis_l_name" type="text" class="frm_fld" id="vis_l_name"<?php getStickyInsert('vis_l_name'); ?> /></td>
    </tr>
    <?php if (isset($vis_email)) { ?>
      <tr>
        <th>&nbsp;</th>
        <td class="frm_warn"><?php echo $vis_email; ?></td>
      </tr>
      <?php } ?>
    <tr>
      <th scope="row"><label for="vis_email">Email address:</label></th>
      <td><input name="vis_email" type="text" class="frm_fld" id="vis_email"<?php getStickyInsert('vis_email'); ?> /></td>
    </tr>
    <?php if (isset($vis_password)) { ?>
      <tr>
        <th>&nbsp;</th>
        <td class="frm_warn"><?php echo $vis_password; ?></td>
      </tr>
      <?php } ?>
    <tr>
      <th scope="row"><label for="vis_password">Password:</label></th>
      <td><input name="vis_password" type="password" class="frm_fld" id="vis_password"<?php getStickyInsert('vis_password'); ?> /></td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td><label>
          <input type="submit" name="button" id="button" value="Register" />
        </label></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="frm_insert" />
</form>
<?php } ?>
</body>
</html>