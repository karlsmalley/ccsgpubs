<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
?>
<html>
<head>
<title><?php echo $applicationtitle; ?> Update Program Entry</title>
<?php 
include "header.php";

$curpage = 0;
$qstring = "";
$prog_id = 0;
if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = $_POST['qstring'];
}
if (isset($_POST['prog_id']))
{
	$prog_id = $_POST['prog_id']*1;
}
if ($prog_id == 0)
{
?> 
	<font face=arial size=3 color=red ><b>Program  ID Not found -- please use these pages in the proper order</b></font><br><br>
	</body></html>
<?php
	exit();
}
	

$booltypes[0] = "";
$booltypes[1] = "YES";
$booltypes[2] = "NO";
$boolvals[0] = "";
$boolvals[1] = "t";
$boolvals[2] = "f";
$activetypes[0] = "";
$activetypes[1] = "ACTIVE";
$activetypes[2] = "INACTIVE";

$doadd = 0;
if ($prog_id == -99)
{
	$doadd = 1;
}
if (!$doadd)
{
	$query = 'select * from programs where rowid = $1';
	$result = pg_query_params($mydbh, $query,array($prog_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);


	$program_name = $row["program_name"];
	$program_code = $row["program_code"];
	$iscurrprog = $row["iscurrprog"];
	$isaltprog1 = $row["isaltprog1"];
	$isaltprog2 = $row["isaltprog2"];
	$isactive = $row["isactive"];
	$sortnum =  $row["sortnum"];

}
else
{
	$query = 'select max(sortnum)+10 from programs where sortnum != 999';
	$result = pg_query_params($mydbh, $query,array());
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);
	$sortnum =  $row[0]*1;
	
	$program_name = "";
	$program_code = "";
	$iscurrprog = "";
	$isaltprog1 = "";
	$isaltprog2 = "";
	$isactive = "";
}	

?>
<table width="100%"><tr><td align="center">
	<form name="returntolist" method="post" action="modprograms.php">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	</form>
	<form name="saveupdates" method="post" action="saveprograms.php">
	<input type="hidden" name="doadd" value="<?php echo $doadd ?>">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	<input type="hidden" name="prog_id" value="<?php echo $prog_id ?>">
				<table cellpadding="10">
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Active (will show up in menus): &nbsp;&nbsp;&nbsp;&nbsp; 
			<select name="isactive">
<?php
			$i = 0;
			while ($i < 3)
			{
				$seltxt = "";
				if ($isactive == $boolvals[$i])
				{
					$seltxt = " selected ";
				}
				echo '<option value="' . $boolvals[$i] . '"  ' . $seltxt . ' >' . $activetypes[$i] . '</option>';
				echo "\n";
				$i++;
			}
?>
			</select></td></tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Program Name: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="program_name" type="text" size="60" value="<?php echo $program_name; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Program Code (will be displayed in publication lists - should be short -- less than 5 characters): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="program_code" type="text" size="5" value="<?php echo $program_code; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Is a Current Program: &nbsp;&nbsp;&nbsp;&nbsp; 
			<select name="iscurrprog">
<?php
			$i = 0;
			while ($i < 3)
			{
				$seltxt = "";
				if ($iscurrprog == $boolvals[$i])
				{
					$seltxt = " selected ";
				}
				echo '<option value="' . $boolvals[$i] . '"  ' . $seltxt . ' >' . $booltypes[$i] . '</option>';
				echo "\n";
				$i++;
			}
?>
			</select></td></tr>
			<tr>
			<td align="left"><font face="Arial, Helvetica, sans-serif">Is a Program in Proposed Structure 1: &nbsp;&nbsp;&nbsp;&nbsp; 
			<select name="isaltprog1">
<?php
			$i = 0;
			while ($i < 3)
			{
				$seltxt = "";
				if ($isaltprog1 == $boolvals[$i])
				{
					$seltxt = " selected ";
				}
				echo '<option value="' . $boolvals[$i] . '"  ' . $seltxt . ' >' . $booltypes[$i] . '</option>';
				echo "\n";
				$i++;
			}
?>
			</select></td></tr>
			<tr>
			<td align="left"><font face="Arial, Helvetica, sans-serif">Is a Program in Proposed Structure 2: &nbsp;&nbsp;&nbsp;&nbsp; 
			<select name="isaltprog2">
<?php
			$i = 0;
			while ($i < 3)
			{
				$seltxt = "";
				if ($isaltprog2 == $boolvals[$i])
				{
					$seltxt = " selected ";
				}
				echo '<option value="' . $boolvals[$i] . '"  ' . $seltxt . ' >' . $booltypes[$i] . '</option>';
				echo "\n";
				$i++;
			}
?>
			</select></td></tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Sort Number (number used to sort programs in different listings throughout the application): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="sortnum" type="text" size="5" value="<?php echo $sortnum; ?>"> </td>
						</tr>
	
						
						
			<tr><td  align="center"><input type="submit" value="Save Changes"><br /><br />
<a href="javascript:returntolist.submit();">Return to Program Listing</a>

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