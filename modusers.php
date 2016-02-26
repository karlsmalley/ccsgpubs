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

$rowsperpage = 10;
$curpage  = 1;

$qstring = "";
if (isset($_POST["curpage"]))
{
	$curpage = $_POST["curpage"];
}
if (isset($_POST["qstring"]))
{
	$qstring = $_POST["qstring"];
}
$whereclause = " ";
$begrec = ($curpage -1) * $rowsperpage + 1;
$currec = $begrec;
$limitclause = " ";
$doprev = 0;
$qstring = strtoupper ($qstring);
$wheredescript = "";
if ($curpage > 1)
{
	$doprev = 1;
	$limitclause = " offset " . ($begrec-1) . " ";
}
$valarray = array(); 
if (strlen($qstring) > 0)
{
	$whereclause = " where upper(lastname ||  ' ' || firstname) like $1 ";
	$wheredescript = " where the users name contains " .  strtoupper(trim($qstring));
	$valarray[0] =  "%" . strtoupper(trim($qstring)) . "%";
} 
?>
<html>
<head>
<script type="text/javascript">
function dodeluser(userid)
{
	document.deluser.user_id.value = userid;
	document.deluser.submit();
}
function domoduser(userid)
{
	document.moduser.user_id.value = userid;
	document.moduser.submit();
}
</script>
<title><?php echo $applicationtitle; ?> -  Users Maintenance Menu</title>
<?php include "header.php"; ?>
<?php
//echo "whereclause: " . $whereclause ."<br>";
//echo "limitclause: " . $limitclause ."<br>";
//echo "wheredescript: " . $wheredescript ."<br>";
$errormsg = "";
if (isset($_POST["action"]))
{
	$action = $_POST["action"];
	if ($action == "DELETE")
	{
		if (isset($_POST["user_id"]))
		{
			$user_id = $_POST["user_id"]*1;
			$query = 'delete from ccsg_users where user_id = $1';
			//	echo $query ."<br>";
			$result = pg_query_params($mydbh, $query, array($user_id));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
		}
		else
		{
			$erromsg= "Could Not Delete User. User ID was missing.";
		}
 	}
}
?>
	<form name="deluser" method="post" action="modusers.php">
    <input type="hidden" name="curpage" value="<?php echo (curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="user_id">
    <input type="hidden" name="action" value="DELETE">
 	</form>
	<form name="moduser" method="post" action="updusers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="user_id">
 	</form>
	<form name="adduser" method="post" action="updusers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="user_id" value="-99">
 	</form>
	
	<form name="prevpage" method="post" action="modusers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage - 1); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>
	<form name="nextpage" method="post" action="modusers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage + 1); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>

				
<?php
$query = "SELECT count(*) FROM ccsg_users " . $whereclause;
//echo "query: " . $query ."<br>";
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$row = pg_fetch_array($result);
$totcnt = $row[0];

$query = "SELECT * FROM ccsg_users " . $whereclause . " order by lastname,firstname " . $limitclause;
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
//echo "query: " . $query ."<br>";

$i = 1;
while(($row = pg_fetch_array ($result)) && ($i <= ($rowsperpage+1)))
{
	//print_r($row);
	$duser_id[$i] = $row["user_id"];
	$thisuser = $row["user_id"];
	$dlastname[$i] = $row["lastname"];
	$dfirstname[$i] = $row["firstname"];
	$dcampuskey[$i] = $row["campuskey"];
	$dusertype[$i] = $row["usertype"];
	$demail[$i] = $row["email"];
	$i++;
}
if ($i > ($rowsperpage))
{
	$donext = 1;
	$i = $rowsperpage;
}
else
{
	$donext = 0;
	$i--;
}

$numusers = $i;
$lastrec = $currec + $numusers -1;					 
?>
<table width="100%"><tr><td align="center">
<table>
<tr><td colspan = "6" align="center"><font face="Arial, Helvetica, sans-serif" size="4">User List</td></tr>
<tr><td align="center" colspan = "6"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" ><b>
Displaying Records <?php echo $currec; ?> - <?php echo $lastrec; ?> of <?php echo $totcnt; ?> records <?php echo $wheredescript; ?>
</b><br><br></td></tr>
<form name="clearsearch" action="modusers.php" method="POST">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="qstring" value="">
</form>
<form name="search" action="modusers.php" method="POST">
<input type="hidden" name="curpage" value="1">
<tr><td colspan="6" nowrap="nowrap"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >
Search By Name: <input type="text" size="10" name="qstring" value="<?php echo $qstring; ?>">
<input type="submit" value="Search"><input type="button"  value="Clear Search" onClick="javascript:document.clearsearch.submit();">
<br><br></td></tr>
<?php

echo '<tr><td align="center" valign="bottom" colspan="2">';
if ($doprev)
{
	echo '<input type="button" onClick="javascript:prevpage.submit();" value="<">';
}
else
{
	echo '<input type="button"  disabled value="<">';
}
echo "</td>";

?>
<td colspan="2" align="center" valign="bottom">
<input type="button" onClick="javascript:document.adduser.submit();" value="Add a User">
</td>
<?php
echo '<td align="center" valign="bottom" colspan="2">';
if ($donext)
{
	echo '<input type="button" onClick="javascript:nextpage.submit();" value=">">';
}
else
{
	echo '<input type="button" disabled value=">">';
}
echo "</td></form></tr>";
?>
<tr>
<tr bgcolor="#0000A0">
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Del. User</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Last Name</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >First Name</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >CampusKey</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >E-Mail</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >User Type</font></td>
<?php
$dowhite = 1;
$i = 1;
while($i <= $numusers)
{
	$bgcolor = "#F2F2F2";
	if ($dowhite)
	{
		$bgcolor = "#FFFFFF";
		$dowhite = 0;
	}
	else
	{
		$dowhite = 1;
	}
    echo '<tr bgcolor="' . $bgcolor . '">';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<input type="button" value="X" onClick="javascript:dodeluser(' . $duser_id[$i] . ');">';
    echo '<td ><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo '<a href="javascript:domoduser(' . $duser_id[$i] . ');">' . $dlastname[$i];
	 echo '</a></td>';
    echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $dfirstname[$i];
	 echo '</td>';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $dcampuskey[$i];
	 echo '</td>';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $demail[$i];
	 echo '</td>';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $dusertype[$i];
	 echo '</td>';
    echo '</tr>';
   	$i++;
 }
?>
<td colspan="6" align="center"><br /><br />
<a href="menu.php">Return to Main Menu</a><br><br>
</td></tr></table></td></tr></table></form>
<?php
pg_close($mydbh);
include("footer.php");
?>