<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
if ($usertype != "ADMIN")
{
?> 
	<html>
	<head>
	<title><?php echo $applicationtitle; ?> - Not Authorized For This Function</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
	<font face=arial size=3 color=red ><b>You Are Not Authorized To Use This Part of the Application.</b></font><br><br>
	</body></html>
<?php
	exit();
}
?>
<html>
<head>
<title><?php echo $applicationtitle; ?> Update User Entry</title>
<?php 
include "header.php";

$curpage = 0;
$qstring = "";
$user_id = 0;
if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = $_POST['qstring'];
}
if (isset($_POST['user_id']))
{
	$user_id = $_POST['user_id']*1;
}
if ($user_id == 0)
{
?> 
	<font face=arial size=3 color=red ><b>User  ID Not found -- please use these pages in the proper order</b></font><br><br>
	</body></html>
<?php
	exit();
}
	

$usertypes[0] = "";
$usertypes[1] = "ADMIN";
$usertypes[2] = "NORMAL";
$doadd = 0;
if ($user_id == -99)
{
	$doadd = 1;
}
if (!$doadd)
{
	$query = 'select * from ccsg_users where user_id = $1';
	$result = pg_query_params($mydbh, $query,array($user_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);

	$lastname = $row["lastname"];
	$firstname = $row["firstname"];
	$campuskey = $row["campuskey"];
	$usertype = $row["usertype"];
	$email = $row["email"];
}
else
{
	$lastname = "";
	$firstname = "";
	$campuskey = "";
	$usertype = "";
	$email = "";
}	

?>
<table width="100%"><tr><td align="center">
	<form name="returntolist" method="post" action="modusers.php">
	<input type="hidden" name="action" value="<?php echo $action ?>">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	</form>
	<form name="saveupdates" method="post" action="saveusers.php">
	<input type="hidden" name="doadd" value="<?php echo $doadd ?>">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	<input type="hidden" name="user_id" value="<?php echo $user_id ?>">
				<table cellpadding="10">
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Last Name: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="lastname" type="text" size="40" value="<?php echo $lastname; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">First Name: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="firstname" type="text" size="40" value="<?php echo $firstname; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">CampusKey: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="campuskey" type="text" size="40" value="<?php echo $campuskey; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">EMail: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="email" type="text" size="40" value="<?php echo $email; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">User Type &nbsp;&nbsp; 
						<select name="usertype">
						<?php
						$i = 0;
						while ($i <= 3)
						{
								$selstr = "";
								if ($usertypes[$i] == $usertype)
								{
									$selstr = " selected ";
								}
								echo '<option value="' . $usertypes[$i] . '" ' . $selstr . '>' . $usertypes[$i] . '</option>';
								$i++;
							}
							?>
							</select>
						</td>
						</tr>
						
						
			<tr><td  align="center"><input type="submit" value="Save Changes"><br /><br />
<a href="javascript:returntolist.submit();">Return to User Listing</a>

</form>
			</table>
			</td></tr>
    </table>
</form>
</td></tr></table>
<?php
pg_close($mydbh);
include("footer.php");
?>