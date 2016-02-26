<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";

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
	$whereclause = " where program_name like $1 ";
	$wheredescript = " where the program name contains " .  strtoupper(trim($qstring));
	$valarray[0] =  "%" . strtoupper(trim($qstring)) . "%";
} 
?>
<html>
<head>
<script type="text/javascript">
function dodeactivate(prog_id)
{
	document.modactive.prog_id.value = prog_id;
	document.modactive.isactive.value = 'f';
	document.modactive.submit();
}
function doactivate(prog_id)
{
	document.modactive.prog_id.value = prog_id;
	document.modactive.isactive.value = 't';
	document.modactive.submit();
}
function dodelprog(prog_id)
{
	document.delprog.prog_id.value = prog_id;
	document.delprog.submit();
}
function domodprog(prog_id)
{
	document.modprog.prog_id.value = prog_id;
	document.modprog.submit();
}
</script>
<title><?php echo $applicationtitle; ?> -  Research Program Maintenance Menu</title>
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
		if (isset($_POST["prog_id"]))
		{
			$thisprog_id = $_POST["prog_id"]*1;
			$query = 'delete from programs where rowid = $1';
			$result = pg_query_params($mydbh, $query, array($thisprog_id));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
		}
		else
		{
			$erromsg= "Could Not Delete Program. Program ID Was Missing.";
		}
 	}
	if ($action == "MODACTIVE")
	{
		if (isset($_POST["prog_id"]))
		{
			$thisprog_id = $_POST["prog_id"]*1;
			$thisisactive = null;
			if (isset($_POST["isactive"]) && strlen(trim($_POST['isactive'])) > 0)
			{
				$thisisactive = trim($_POST["isactive"]);
			}
			$query = 'update programs set isactive = $1 where rowid = $2';
			$result = pg_query_params($mydbh, $query, array($thisisactive,$thisprog_id));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
		}
		else
		{
			$erromsg= "Could Not Modify Active Status Of Program . Program ID Was Missing.";
		}
 	}
}
?>
	<form name="delprog" method="post" action="modprograms.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="prog_id">
    <input type="hidden" name="action" value="DELETE">
 	</form>
	<form name="modactive" method="post" action="modprograms.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="prog_id">
    <input type="hidden" name="isactive">
    <input type="hidden" name="action" value="MODACTIVE">
 	</form>
	<form name="modprog" method="post" action="updprograms.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="prog_id">
 	</form>
	<form name="addprog" method="post" action="updprograms.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="prog_id" value="-99">
 	</form>
	
	<form name="prevpage" method="post" action="modprograms.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage - 1); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>
	<form name="nextpage" method="post" action="modprograms.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage + 1); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>

				
<?php
$query = "SELECT count(*) FROM programs " . $whereclause;
//echo "query: " . $query ."<br>";
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$row = pg_fetch_array($result);
$totcnt = $row[0];

$query = "SELECT * FROM programs " . $whereclause . " order by sortnum,program_code " . $limitclause;
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
//echo "query: " . $query ."<br>";

$i = 1;
while(($row = pg_fetch_array ($result)) && ($i <= ($rowsperpage+1)))
{
	//print_r($row);
	$prog_id[$i] = $row["rowid"];
	$program_name[$i] = $row["program_name"];
	$program_code[$i] = $row["program_code"];
	$iscurrprog[$i] = $row["iscurrprog"];
	$isaltprog1[$i] = $row["isaltprog1"];
	$isaltprog2[$i] = $row["isaltprog2"];
	$isactive[$i] = $row["isactive"];
	$sortnum[$i] =  $row["sortnum"];

	$i++;
}
if ($i > ($rowsperpage+1))
{
	$donext = 1;
	$i = $rowsperpage;
}
else
{
	$donext = 0;
	$i--;
}
$numprogs = $i;
$lastrec = $currec + $numprogs -1;					 
?>
<table width="100%"><tr><td align="center">
<table>
<tr><td colspan = "8" align="center"><font face="Arial, Helvetica, sans-serif" size="4">Research Program List</td></tr>
<tr><td align="center" colspan = "8"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" ><b>
Displaying Records <?php echo $currec; ?> - <?php echo $lastrec; ?> of <?php echo $totcnt; ?> records <?php echo $wheredescript; ?>
</b><br><br></td></tr>
<form name="clearsearch" action="modprograms.php" method="POST">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="qstring" value="">
</form>
<form name="search" action="modprograms.php" method="POST">
<input type="hidden" name="curpage" value="1">
<tr><td colspan="8" nowrap="nowrap" align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >
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
<td colspan="4" align="center" valign="bottom">
<input type="button" onClick="javascript:document.addprog.submit();" value="Add a Research Program">
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
<tr bgcolor="#0000A0">
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Del Prog.</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Active</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Program Code</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Program Name</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Curr Prog</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Prop Prog 1</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Prop Prog 2</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >SortNum</font></td>
</tr>
<?php
$bgcolor = "#F2F2F2";
$i = 1;
while($i <= $numprogs)
{
	if ($bgcolor == "#FFFFFF")
	{
		$bgcolor = "#F2F2F2";
	}
	else
	{
		$bgcolor = "#FFFFFF";
	}
    echo '<tr bgcolor="' . $bgcolor . '">';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<input type="button" value="X" onClick="javascript:dodelprog(' . $prog_id[$i] . ');"></td>';
	$actfunct = "activate";
	$acttext = 'INACTIVE';
	if ($isactive[$i] =='t')
	{
		$acttext = 'ACTIVE';
		$actfunct = 'deactivate';
	}
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<a href="javascript:do' . $actfunct . '(' . $prog_id[$i] . ');">' . $acttext . '</a></td>';

    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<a href="javascript:domodprog(' . $prog_id[$i]. ');">' . $program_code[$i] . '</a></td>';

    echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<a href="javascript:domodprog(' . $prog_id[$i] . ');">' . $program_name[$i] . '</a></td>';



	$inprog = 'NO';
	if ($iscurrprog[$i] =='t')
	{
		$inprog = 'YES';
	}
	echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $inprog;
	echo '</td>';

	$inprog = 'NO';
	if ($isaltprog1[$i] =='t')
	{
		$inprog = 'YES';
	}
	echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $inprog;
	echo '</td>';
 
	$inprog = 'NO';
	if ($isaltprog2[$i] =='t')
	{
		$inprog = 'YES';
	}
	echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $inprog;
	echo '</td>';


	echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >';
    echo $sortnum[$i];
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