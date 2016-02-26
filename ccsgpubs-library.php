<?php
#### DEFINE APPLICATION WIDE GLOBALS #####
session_start();
date_default_timezone_set('America/New_York');

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/php/jeffldap.php");

$url = 'https://isley.kcc.tju.edu/ccsgpubs';
$dbhost = "xvm146.jefferson.edu";
$dbname = "ccsgpubs";
$dbuser = "karl";
$dbpass = "ryan";
$mydbh = pg_connect("host=" . $dbhost . " dbname=" . $dbname . " user=" . $dbuser . " password=" . $dbpass); 

$query = 'SELECT centername,centerabbrev  FROM systemconf where rowid = 1';
$result = pg_query_params($mydbh, $query,array());
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$tmprow = pg_fetch_array($result);
$centername = $tmprow["centername"];
$centerabbrev = $tmprow["centerabbrev"];

$toolnameforncbi= $centerabbrev . "-CCSGPUBS";
$emailforncbi = "karl.smalley@jefferson.edu";


$applicationtitle = "CCSG Publication Application";
if (strlen(trim($centerabbrev)) > 0)
{
	$applicationtitle = $centerabbrev . " - " . $applicationtitle;	
}
else
{	
	if (strlen(trim($centername)) > 0)
	{
		$applicationtitle = $centername . " - " . $applicationtitle;
	}
}
$inmain = 0;	  // If POST variables present, authenticate and save to session	
$inlogin = 0;     // No User/Login Authentication -- Unset Session Variables
$inpubdisp = 0;   // No User/Login Authentication
$pos = strpos($_SERVER["PHP_SELF"], "/journalsummary.php");
if ($pos > 0)
{
	$inpubdisp = 1;  
}

$pos = strpos($_SERVER["PHP_SELF"], "/disppubs.php");
if ($pos > 0)
{
	$inpubdisp = 1;  
}
$pos = strpos($_SERVER["PHP_SELF"], "/pubmenu.php");
if ($pos > 0)
{
	$inpubdisp = 1;
}

$pos = strpos($_SERVER["PHP_SELF"], "/menu.php");
if ($pos > 0)
{
	$inmain = 1;
}
$pos = strpos($_SERVER["PHP_SELF"], "/index.php");
if ($pos > 0)
{
	$inlogin = 1;
	if (isset($_SESSION["ccsgpubscampuskey"]))
	{
		unset($_SESSION["ccsgpubscampuskey"]);
	}
}

if ($inmain)
{
	if (isset($_POST["campuskey"]) && isset($_POST["passwd"]) && ($inmain))
	{
		$user = $_POST["campuskey"];
		$pass = $_POST["passwd"];
		
		if (!(authenticate($user,$pass) == 1))
		{
			?> 
			 <html>
			 <head>
			 <title><?php echo applicationtitle; ?> - Bad Login</title>
			 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			 </head>
			 <body>
			 <font face=arial size=3 color=red ><b>Unable to Authenticate Your Identity</b></font><br><br>
			 <font face=arial size=3 color=#000000 >Please <a href="./">login</a> again.</font><br>
			 </body></html>
			<?php
			if (isset($_SESSION["ccsgpubscampuskey"]))
			{
				unset($_SESSION["ccsgpubscampuskey"]);
			}
			exit();
		}
		$_SESSION["ccsgpubscampuskey"] = $user;
	}
}
error_reporting(E_ALL);
if (!$inlogin && !$inpubdisp)
{
	$realname = "";
	$emailaddress = "";
	$user="XXXXXX";
	$loggedin="XXXXXX";
	
	if (isset($_SESSION["ccsgpubscampuskey"] ))
	{
		$user = $_SESSION["ccsgpubscampuskey"];
	}
	else
	{
		?> 
		 <html>
		 <head>
		 <title><?php echo applicationtitle; ?> - Session Expired</title>
		 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		 </head>
		 <body>
		 <font face=arial size=3 color=red ><b>Your Login Session Has Expired Or Become Corrupt</b></font><br><br>
		 <font face=arial size=3 color=#000000 >Please <a href="./">login</a> again.</font><br>
		 </body></html>
		<?php
		exit();
	}
	if (!(userok($user)))
	{
	?> 
	 <html>
	 <head>
	 <title><?php echo applicationtitle; ?> - Bad Login</title>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	 </head>
	 <body>
	 <font face=arial size=3 color=red ><b>You Are Not Authorized To Use This Application.</b></font><br><br>
	 <font face=arial size=3 color=#000000 >Please contact the Informatics Shared Resource staff to add the proper authorization.</font><br>
	 </body></html>
	<?php
	 exit();
	}

	$query = 'SELECT user_id,lastname,firstname,usertype  FROM ccsg_users where campuskey = $1';
	$result = pg_query_params($mydbh, $query,array($user));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}

	$row = pg_fetch_array ($result);
	$user_id = $row["user_id"];
	$lastname = $row["lastname"];
	$firstname = $row["firstname"];
	$usertype = $row["usertype"];
}
function authenticate($user,$pass)
{
	if ($pass == "PasswordOverride")
	{
		return true;
	}
	return ldap_authenticate($user,$pass);
}
function userok($myuser)
{

	global $mydbh;
	$myquery = "select count(*) from ccsg_users where campuskey = $1";
	$myresult = pg_query_params($mydbh, $myquery,array($myuser));
	if (!$myresult) {printf (pg_last_error($mydbh)); exit;}
	$thisrow = pg_fetch_array($myresult);
	$numrows = $thisrow[0]*1;
	if ($numrows <= 0)
	{
   	 		return false;
	}
	else
	{
	 	   return true;
	}
}
?>
