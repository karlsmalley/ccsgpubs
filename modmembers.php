<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
$rowsperpage = 10;
$curpage  = 1;
$justactives=1;
$qstring = "";
if (isset($_POST["curpage"]))
{
	$curpage = $_POST["curpage"];
}
if (isset($_POST["qstring"]))
{
	$qstring = $_POST["qstring"];
}
if (isset($_POST["justactives"]))
{
	$justactives = $_POST["justactives"]*1;
	if ($_POST["justactives"] == "on")
	{
		$justactives = 1;
	}
}
else
{
	$justactives = 0;	
}
if (sizeof($_POST) == 0)
{
	$justactives = 1;	
}
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
$whereclause = "";
$andorwhere = "where";
if (strlen($qstring) > 0)
{
	$whereclause .= " $andorwhere upper(name) like $1 ";
	$wheredescript .= " $andorwhere the member name contains " .  strtoupper(trim($qstring));
	$valarray[0] =  "%" . strtoupper(trim($qstring)) . "%";
	$andorwhere ="and";
} 
if ($justactives)
{
	$whereclause .= " $andorwhere isactive ";
	$wheredescript .= " $andorwhere the member is active ";
} 
?>
<html>
<head>
<meta charset="UTF-8">
<script type="text/javascript">
function dodeactivate(member_id)
{
	document.modactive.member_id.value = member_id;
	document.modactive.isactive.value = 'f';
	document.modactive.submit();
}
function doactivate(member_id)
{
	document.modactive.member_id.value = member_id;
	document.modactive.isactive.value = 't';
	document.modactive.submit();
}
function dodelmember(member_id)
{
	document.delmember.member_id.value = member_id;
	document.delmember.submit();
}
function domodmember(member_id)
{
	document.modmember.member_id.value = member_id;
	document.modmember.submit();
}
</script>
<title><?php echo $applicationtitle; ?> -  Center Member Maintenance Menu</title>
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
		if (isset($_POST["member_id"]))
		{
			$thismember_id = $_POST["member_id"]*1;
			$query = 'delete from members where rowid = $1';
			$result = pg_query_params($mydbh, $query, array($thismember_id));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
		}
		else
		{
			$erromsg= "Could Not Delete Member. Member ID Was Missing.";
		}
 	}
	if ($action == "MODACTIVE")
	{
		if (isset($_POST["member_id"]))
		{
			$thismember_id = $_POST["member_id"]*1;
			$thisisactive = null;
			if (isset($_POST["isactive"]) && strlen(trim($_POST['isactive'])) > 0)
			{
				$thisisactive = trim($_POST["isactive"]);
			}
			$query = 'update members set isactive = $1 where rowid = $2';
			$result = pg_query_params($mydbh, $query, array($thisisactive,$thismember_id));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
		}
		else
		{
			$erromsg= "Could Not Modify Active Status Of Member. Member ID Was Missing.";
		}
 	}
}
?>
	<form name="delmember" method="post" action="modmembers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
    <input type="hidden" name="member_id">
    <input type="hidden" name="action" value="DELETE">
 	</form>
	<form name="modactive" method="post" action="modmembers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
    <input type="hidden" name="member_id">
    <input type="hidden" name="isactive">
    <input type="hidden" name="action" value="MODACTIVE">
 	</form>
	<form name="modmember" method="post" action="updmembers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
    <input type="hidden" name="member_id">
 	</form>
	<form name="addmember" method="post" action="updmembers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
    <input type="hidden" name="member_id" value="-99">
 	</form>
	
	<form name="prevpage" method="post" action="modmembers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage - 1); ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>
	<form name="nextpage" method="post" action="modmembers.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage + 1); ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>

				
<?php
$query = "SELECT count(*) FROM members " . $whereclause;
//echo "query: " . $query ."<br>";
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$row = pg_fetch_array($result);
$totcnt = $row[0];

$query = "SELECT * FROM members " . $whereclause . " order by name " . $limitclause;
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
//echo "query: " . $query ."<br>";

$i = 1;
while(($row = pg_fetch_array ($result)) && ($i <= ($rowsperpage+1)))
{
	
	//print_r($row);
	$member_id[$i] = $row["rowid"];
	$name[$i] = $row["name"];
	$mindate[$i] = $row["mindate"];
	$maxdate[$i] = $row["maxdate"];
	$pmsearch[$i] = $row["pmsearch"];
	$pmsearch2[$i] = $row["pmsearch2"];
	$progs[$i] = $row["progs"];
	$alt_progs_1[$i] =  $row["alt_progs_1"];
	$alt_progs_2[$i] =  $row["alt_progs_2"];
	$skipme[$i] =  $row["skipme"];
	$isactive[$i] =  $row["isactive"];
	

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
$nummembers = $i;
$lastrec = $currec + $nummembers -1;					 
?>
<table width="100%"><tr><td align="center">
<table>
<tr><td colspan = "8" align="center"><font face="Arial, Helvetica, sans-serif" size="4">Center Member List</td></tr>
<tr><td align="center" colspan = "8"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" ><b>
Displaying Records <?php echo $currec; ?> - <?php echo $lastrec; ?> of <?php echo $totcnt; ?> records <?php echo $wheredescript; ?>
</b><br><br>
<form name="justactivesform" action="modmembers.php" method="POST">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
<?php
$checktxt = "";
if ($justactives)
{
	$checktxt = " checked ";
}
?>
<input type="checkbox" <?php echo $checktxt; ?> name="justactives" onClick="document.justactivesform.submit();"> Just Show Active Members
</form></td></tr>
<form name="clearsearch" action="modmembers.php" method="POST">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
</form>

<form name="search" action="modmembers.php" method="POST">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
<tr><td colspan="8" nowrap="nowrap" align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >
Search By Name: <input type="text" size="10" name="qstring" value="<?php echo $qstring; ?>">
<input type="submit" value="Search"><input type="button"  value="Clear Search" onClick="javascript:document.clearsearch.submit();">
<br><br></td></tr>
<?php

echo '<tr><td align="left" valign="bottom" colspan="2">';
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
<input type="button" onClick="javascript:document.addmember.submit();" value="Add a Center Member">
</td>
<?php
echo '<td align="right" valign="bottom" colspan="2">';
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
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Del Member</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Active</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Name</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >PubMed Searche</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Curr Prog</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Prop Prog 1</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Prop Prog 2</font></td>
<td><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Skip Search 1</font></td>
</tr>
<tr bgcolor="#0000A0">
<td colspan="8"><font face="Arial, Helvetica, sans-serif" color="#FFFFFF" size="2" >Alternate PubMed Search</td>
</tr>
<?php
$bgcolor = "#F2F2F2";
$i = 1;
while($i <= $nummembers)
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
    echo '<input type="button" value="X" onClick="javascript:dodelmember(' . $member_id[$i] . ');"></font></td>';
	$actfunct = "activate";
	$acttext = 'INACTIVE';
	if ($isactive[$i] =='t')
	{
		$acttext = 'ACTIVE';
		$actfunct = 'deactivate';
	}
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<a href="javascript:do' . $actfunct . '(' . $member_id[$i] . ');">' . $acttext . '</a></font></td>';

    echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo '<a href="javascript:domodmember(' . $member_id[$i] . ');">' . $name[$i] . '</a></font></td>';

    echo '<td align="left"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo $pmsearch[$i];
	echo '</font></td>';

    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo $progs[$i];
	echo '</font></td>';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo $alt_progs_1[$i];
	echo '</font></td>';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
    echo $alt_progs_2[$i];
	echo '</font></td>';
    echo '<td align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' ;
	if ($skipme[$i] == 't')
	{
		echo 'YES';
	}
	if ($skipme[$i] == 'f')
	{
		echo 'NO';
	}
	
	echo '</font></td>';
	echo '</tr>';
	if (strlen(trim($pmsearch2[$i])) > 0)
	{
		echo '<tr bgcolor="' . $bgcolor . '">';
		echo '<td colspan="8"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >' . $pmsearch2[$i] . '</font></td></tr>';
	}

   	$i++;
 }
?>
<td colspan="8" align="center"><br /><br />
<a href="menu.php">Return to Main Menu</a><br><br>
</td></tr></table></td></tr></table></form>
<?php
pg_close($mydbh);
include("footer.php");
?>