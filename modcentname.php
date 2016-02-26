<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
?>
<html>
<head>
<title><?php echo $applicationtitle; ?> Update Center Name</title>
<?php 
include "header.php";
$query = 'select * from systemconf where rowid=1';
$result = pg_query_params($mydbh, $query,array());
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$row = pg_fetch_array($result);
$lastmod = $row["lastmod"];
$centername = $row["centername"];
$centerabbrev = $row["centerabbrev"];
$consortium = $row["consortium"];
$historical_program_alignment = $row["historical_program_alignment"];
$proposed_alignment_1 = $row["proposed_alignment_1"];
$proposed_alignment_2 = $row["proposed_alignment_2"];
$consoptvals[0] = '';
$consoptvals[1] = 't';
$consoptvals[2] = 'f';
$consoptlabels[0] = '';
$consoptlabels[1] = 'Yes';
$consoptlabels[2] = 'No';

?>
<table width="100%"><tr><td align="center">
	<form name="saveupdates" method="post" action="savecentername.php">
				<table cellpadding="10">
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Last Publication Update: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="lastmod" type="text" size="20" value="<?php echo $lastmod; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Center Full Name: &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="centername" type="text" size="60" value="<?php echo $centername; ?>"> </td>
						</tr>
				<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Center Name (Abbreviation): &nbsp;&nbsp;&nbsp;&nbsp; 
            <input name="centerabbrev" type="text" size="40" value="<?php echo $centerabbrev; ?>"> </td>
						</tr>
		<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Label for Program Alignment 1 (Alignment at time of Publications):<br /> 
            <textarea name="historical_program_alignment" cols="80" rows="4"><?php echo $historical_program_alignment; ?></textarea></td>
						</tr>				
		<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Label for Program Alignment 2 (Proposed Alignment): <br /> 
            <textarea name="proposed_alignment_1" cols="80" rows="4"><?php echo $proposed_alignment_1; ?></textarea></td>
						</tr>						
		<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Label for Program Alignment 3 (Proposed Alignment): <br /> 
            <textarea  name="proposed_alignment_2"cols="80" rows="4"><?php echo $proposed_alignment_2; ?></textarea></td>
						</tr>						
						
						
						<tr>
						<td align="left"><font face="Arial, Helvetica, sans-serif">Consortium: &nbsp;&nbsp;&nbsp;&nbsp; 
            <select name="consortium"> 
<?php
$i = 0;
while ($i < 3)
{
	$seltxt = "";
	if ($consortium == $consoptvals[$i])
	{
			$seltxt = " selected ";
	}
	echo '<option value="' . $consoptvals[$i] . '" ' . $seltxt . ' >' . $consoptlabels[$i] . '</option>';
	echo  "\n";
	$i++;
}
?>	
			</select></td>
			</tr>
			<tr><td  align="center"><input type="submit" value="Save Changes"><br /><br />
<a href="./menu.php">Return to Main Menu</a>

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