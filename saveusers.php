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

$curpage = 0;
$qstring = "";
$user_id = 0;


$lastname = null;
$firstname = null;
$campuskey = null;
$usertype = null;
$email = null;


if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = trim($_POST['qstring']);
}
if (isset($_POST['user_id']))
{
	$user_id = $_POST['user_id']*1;
}
if (isset($_POST['campuskey']) and strlen(trim($_POST['campuskey'])) > 0)
{
	$campuskey = strtolower(trim($_POST['campuskey']));
}
if (isset($_POST['lastname']) and strlen(trim($_POST['lastname'])) > 0)
{
	$lastname = trim($_POST['lastname']);
}
if (isset($_POST['firstname']) and strlen(trim($_POST['firstname'])) > 0)
{
	$firstname = trim($_POST['firstname']);
}
if (isset($_POST['usertype']) and strlen(trim($_POST['usertype'])) > 0)
{
	$usertype = trim($_POST['usertype']);
}
if (isset($_POST['email']) and strlen(trim($_POST['email'])) > 0)
{
	$email = trim($_POST['email']);
}


if ($user_id == 0)
{
?> 
	</head>
	<body>
	<font face=arial size=3 color=red ><b>User  ID Not found -- please use these pages in the proper order</b></font><br><br>
	</body></html>
<?php
	exit();
}
if ($campuskey == '')
{
?> 
	</head>
	<body>
	<font face=arial size=3 color=red ><b>You Must Supply A Campus Key -- User Update Aborted!!</b></font><br><br>
	</body></html>
<?php
	exit();
}

if ($user_id == -99)
{
	$query = 'select count(*) from ccsg_users where lower(trim(campuskey)) = $1';
	$result = pg_query_params($mydbh, $query,array($campuskey));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);
	if (($row[0]*1) > 0)
	{
?> 
	</head>
	<body>
	<font face=arial size=3 color=red ><b>CampusKey is Already On File For Another User -- User Update Aborted!!</b></font><br><br>
	</body></html>
<?php
	exit();
	}
}
if ($user_id != -99)
{
	$query = 'select count(*) from ccsg_users where user_id = $1';
	$result = pg_query_params($mydbh, $query,array($user_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);
	if (($row[0]*1) < 1)
	{
?> 
	</head>
	<body>
	<font face=arial size=3 color=red ><b>Could Not Find User Record With Supplied User_ID To Update -- User Update Aborted!!</b></font><br><br>
	</body></html>
<?php
		exit();
	}
}

?>
<html>
<head>
<title><?php echo $applicationtitle; ?> - Save User Updates</title>
<?php include "header.php"; ?>
<form name="returntolist" action="modusers.php" method="POST">
<input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
<input type="hidden" name="curpage" value="<?php echo $curpage; ?>">
</form>

<?php


if ($user_id == -99)
{
	$query = 'INSERT into ccsg_users (lastname,firstname,campuskey,usertype,email) values ($1,$2,$3,$4,$5)';
	$result = pg_query_params($mydbh, $query,array($lastname, $firstname, $campuskey, $usertype,$email));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The User Has Been Added.<br><br>';
	echo '</font><a href="javascript:returntolist.submit();">Return to User Listing</a>';	
}
else
{
	
   
	$query = 'update ccsg_users set lastname = $1, firstname=$2 ,campuskey=$3, usertype=$4, email=$5 '; 
	$query .= ' where user_id = $6';
	//echo $query . "<br>";
	$result = pg_query_params($mydbh, $query,array($lastname, $firstname, $campuskey, $usertype,$email,$user_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The User Updates Have Been Saved.<br><br>';
	echo '</font><a href="javascript:returntolist.submit();">Return to User Listing</a>';	
}

include('footer.php');
pg_close($mydbh);
?>

</body>
</html>
