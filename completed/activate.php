<?php require_once('Connections/conndb.php'); ?>
<?php
if (isset($_GET['id']) && $_GET['id'] != '') {
	
	$id = $_GET['id'];
	
	mysql_select_db($database_conndb, $conndb);
	$query_rsHash = "SELECT usr_id, usr_active FROM tbl_users WHERE usr_hash = '".$id."'";
	$rsHash = mysql_query($query_rsHash, $conndb) or die(mysql_error());
	$row_rsHash = mysql_fetch_assoc($rsHash);
	$totalRows_rsHash = mysql_num_rows($rsHash);
	
	if ($totalRows_rsHash > 0) {
		
		if ($row_rsHash['usr_active'] == 'y') {
		
			$content = '<p>This account has already been activated</p>';
			
		} else {
			
			$updateSQL = sprintf("UPDATE tbl_users SET usr_active = 'y' WHERE usr_id = ".$row_rsHash['usr_id']);
	  	
	 		 mysql_select_db($database_conndb, $conndb);
	  		$Result2 = mysql_query($updateSQL, $conndb) or die(mysql_error());
			
			$content = '<p>Your account has been successfully activated</p>';
			
		}
		
	} else {
		
		$content = '<p>You have accessed this page incorrectly</p>';	
		
	}
	
	mysql_free_result($rsHash);
	
} else {

	$content = '<p>You have accessed this page incorrectly</p>';
	
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
<?php if (isset($content)) { echo $content; } ?>
</body>
</html>
