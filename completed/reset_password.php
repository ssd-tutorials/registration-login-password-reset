<?php require_once('Connections/conndb.php'); ?>
<?php
if (isset($_POST['resetpassword']) && $_POST['resetpassword'] == 'resetnow') {
	
	mysql_select_db($database_conndb, $conndb);
	$query_rsEmail = "SELECT usr_email FROM tbl_users WHERE usr_email = '".$_POST['frm_reset_email']."'";
	$rsEmail = mysql_query($query_rsEmail, $conndb) or die(mysql_error());
	$row_rsEmail = mysql_fetch_assoc($rsEmail);
	$totalRows_rsEmail = mysql_num_rows($rsEmail);
	
	if ($totalRows_rsEmail > 0) {
		
		$newpass = substr(md5(rand().rand()), 0, 7);
		$newpassword = md5($newpass);
		
		$updateSQL = "UPDATE tbl_users SET usr_password = '".$newpassword."' WHERE usr_email = '".$_POST['frm_reset_email']."'";
		mysql_select_db($database_conndb, $conndb);
		$Result = mysql_query($updateSQL, $conndb) or die (mysql_error());
		
		if ($Result) {
			
			$to = $_POST['frm_reset_email'];
			$subject = 'Your new password';
			$from = 'dummy@emailaddress.co.uk';
			
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8\r\n";
			$headers .= "From: My Website Name <".$from.">\r\n";
			$headers .= "Reply-to: My Website Name <".$from.">\r\n";
			
			$message = '<div style="font-family:Arial, Verdana, Sans-serif; color:#333; font-size:12px">
			<p>You have requested the new password.</p>
			<p>Your new password is: '.$newpass.'</p></div>';
			
			if (mail($to, $subject, $message, $headers)) {
				
				$res = '<p>Your new password has been sent to your registered email address.</p>';
				
			} else {
				
				$res = '<p>Your new password could not be sent.<br />Please contact administrator.</p>';
				
			}
			
		} else {
			
			$res = '<p>Your new password could not be generated.<br />Please contact administrator.</p>';	
			
		}
		
	} else {
		
		$mess = 'This email address is not registered on our system';	
		
	}
	
	mysql_free_result($rsEmail);
	
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
<?php if (isset($res)) { echo $res; } ?>
<form id="frm_reset" name="frm_reset" method="post" action="">
  <table border="0" cellspacing="0" cellpadding="0" id="tbl_insert">
    <?php if (isset($mess)) { ?>
      <tr>
        <th>&nbsp;</th>
        <td><?php echo $mess; ?></td>
      </tr>
      <?php } ?>
    <tr>
      <th scope="row"><label for="frm_reset_email">Email address:</label></th>
      <td><input name="frm_reset_email" type="text" class="frm_fld" id="frm_reset_email" /></td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td><label>
          <input type="submit" name="button" id="button" value="Reset password" />
        </label></td>
    </tr>
  </table>
  <input type="hidden" name="resetpassword" value="resetnow" />
</form>
</body>
</html>
