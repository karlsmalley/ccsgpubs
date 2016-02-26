<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
?>
<html>
<head>
<title><?php echo $applicationtitle; ?> Update Center Member Entry</title>
<?php 
include "header.php";

$curpage = 0;
$qstring = "";
$justactives = "";

$member_id = 0;
if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = $_POST['qstring'];
}
if (isset($_POST['justactives']))
{
	$justactives = $_POST['justactives']*1;
}
if (isset($_POST['member_id']))
{
	$member_id = $_POST['member_id']*1;
}
if ($member_id == 0)
{
?> 
	<font face=arial size=3 color=red ><b>Member ID Not found -- please use these pages in the proper order</b></font><br><br>
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
if ($member_id == -99)
{
	$doadd = 1;
}
if (!$doadd)
{
	$query = 'select * from members where rowid = $1';
	$result = pg_query_params($mydbh, $query,array($member_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);
  
	$name = $row["name"];
	$mindate = $row["mindate"];
	$maxdate = $row["maxdate"];
	$pmsearch = $row["pmsearch"];
	$pmsearch2 = $row["pmsearch2"];
	$progs = $row["progs"];
	$alt_progs_1 = $row["alt_progs_1"];
	$alt_progs_2 = $row["alt_progs_2"];
	$isactive = $row["isactive"];
	$skipme =  $row["skipme"];

}
else
{
	
	$name = "";
	$mindate = "";
	$maxdate = "";
	$pmsearch = "";
	$pmsearch2 = "";
	$progs = "";
	$alt_progs_1 = "";
	$alt_progs_2 = "";
	$isactive = "";
	$skipme  = "";
}	

?>
<table width="100%"><tr><td align="center">
	<form name="returntolist" method="post" action="modmembers.php">
	<input type="hidden" name="justactives" value="<?php echo $justactives ?>">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	</form>
	<form name="saveupdates" method="post" action="savemembers.php">
	<input type="hidden" name="doadd" value="<?php echo $doadd ?>">
	<input type="hidden" name="justactives" value="<?php echo $justactives ?>">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	<input type="hidden" name="member_id" value="<?php echo $member_id ?>">
				<table cellpadding="10">
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Active (PubMed Searches Will Be Performed In Batch Updates): &nbsp;&nbsp;&nbsp;&nbsp; 
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
						<td align="left"><font face="Arial, Helvetica, sans-serif">Name (LastName, FirstName): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="name" type="text" size="40" value="<?php echo $name; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Minimum Date (Date Valid To Begin Collecting Publications): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="mindate" type="date" size="15" value="<?php echo $mindate; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Maximum Date (Date To Stop Collecting Publications - Use 1/1/2101 for null): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="maxdate" type="date" size="15" value="<?php echo $maxdate; ?>"> </td>
						</tr>
				<tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Main PubMed Search (LASTNAME INTIAL(S) also used to highlight author in curation module): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="pmsearch" type="text" size="25" value="<?php echo $pmsearch; ?>"> </td>
						</tr>
				<td align="left"><font face="Arial, Helvetica, sans-serif">Skip Pimary PM Search (There is reason to even try searches like Jones J or  Zhang J): &nbsp;
				<select name="skipme">
<?php
			$i = 0;
			while ($i < 3)
			{
				$seltxt = "";
				if ($skipme == $boolvals[$i])
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
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Secondary PM Search (For Those Members Where LastName and Initial Just Won't Do):<br />
						 
            <textarea name="pmsearch2" rows="4" cols="100"><?php echo $pmsearch2; ?></textarea></td>
						</tr>
			<tr><td>
			<table border="2"><tr>
			<td align="center"><font face="Arial, Helvetica, sans-serif"><b>Current Programs</b></font></td>
			<td align="center"><font face="Arial, Helvetica, sans-serif"><b>Proposed Programs 1</b></font></td>
			<td align="center"><font face="Arial, Helvetica, sans-serif"><b>Proposed Programs 2</b></font></td>
			<tr>
<?php
			$pquery = "select * from programs where isactive order by sortnum ";
			$presult = pg_query_params($mydbh,$pquery,array());
			if (!$presult) {printf (pg_last_error($mydbh)); exit;}
			$numcp = 0;
			$numap1 = 0;
			$numap2 = 0;
			while($prow = pg_fetch_array($presult) )
			{
				$thisprog = $prow['program_code'];
// Can not use pipe character (|) in program abbreviations
				$tmpstr = 'ZZ|' . $progs . '|';
				if ($prow['iscurrprog'] == 't')
				{
					$currproglist[$numcp] = $thisprog;
					$curcheck[$numcp] = "";						
					if (strpos($tmpstr,"|" . $thisprog ."|") > 0)
					{
						$curcheck[$numcp] = "checked";
					}
					$numcp++;
				}
				$tmpstr = 'ZZ|' . $alt_progs_1 . '|';
				if ($prow['isaltprog1'] == 't')
				{
					$altprog1list[$numap1] = $thisprog;
					$alt1check[$numap1] = "";						
					if (strpos($tmpstr,"|" . $thisprog ."|") > 0)
					{
						$alt1check[$numap1] = "checked";
					}
					$numap1++;
				}
				$tmpstr = 'ZZ|' . $alt_progs_2 . '|';
				if ($prow['isaltprog2'] == 't')
				{
					$altprog2list[$numap2] = $thisprog;
					$alt2check[$numap2] = "";						
					if (strpos($tmpstr,"|" . $thisprog . "|") > 0)
					{
						$alt2check[$numap2] = "checked";
					}
					$numap2++;
				}
				
			}
			echo "\n";
			echo '<tr>';
			echo '<td valign="top"><font face="Arial, Helvetica, sans-serif">';
			$j = 0;
			$break = "";
			while ($j < $numcp)
			{
				echo $break . '<input type="checkbox" name="currprog_' . $currproglist[$j] . '" ' . $curcheck[$j] . '> ' . $currproglist[$j];
				$break='<br />';
				echo "\n";
				$j++;
			}
			echo "</td>";
			


			echo '<td valign="top"><font face="Arial, Helvetica, sans-serif">';
			$j = 0;
			$break = "";
			while ($j < $numap1)
			{
				echo $break . '<input type="checkbox" name="altprog1_' . $altprog1list[$j] . '" ' . $alt1check[$j] . '> ' . $altprog1list[$j];
				$break='<br />';
				echo "\n";
				$j++;
			}
			echo "</td>";


			echo '<td valign="top"><font face="Arial, Helvetica, sans-serif">';
			$j = 0;
			$break = "";
			while ($j < $numap2)
			{
				echo $break . '<input type="checkbox" name="altprog2_' . $altprog2list[$j] . '" ' . $alt2check[$j] . '> ' . $altprog2list[$j];
				$break='<br />';
				echo "\n";
				$j++;
			}
			echo "</td>";
			echo '</tr></table>';
			echo '</td></tr>';
?>
						
						
			<tr><td  align="center"><input type="submit" value="Save Changes"><br /><br />
<a href="javascript:returntolist.submit();">Return to Member Listing</a>

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