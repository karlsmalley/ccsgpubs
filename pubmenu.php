<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
// This script should be available to anyone who might need to see the publications  and not necessarily someone who might need to modify/update publications.
// For this reason it is launched as a new Window/Tab for the rest of the application.. Has no links back to the application. 
// Nor does it use session variables created by the application.
// However we would still like tio include the common library file.

require "ccsgpubs-library.php";
?>
<html>
<head>
<script>
function copyparameters(theform)
{
	var paramform = eval("document.myparameters");
	if (paramform.enumerate.checked)
	{
		theform.enumerate.value = 1;
	}
	else
	{
		theform.enumerate.value = 0;
	}
	if (paramform.showdate.checked)
	{
		theform.skipdate.value = 0;
	}
	else
	{
		theform.skipdate.value = 1;
	}
	if (paramform.showabstract.checked)
	{
		theform.showabstract.value = 1;
	}
	else
	{
		theform.showabstract.value = 0;
	}
	if (paramform.showrowid.checked)
	{
		theform.showrowid.value = 1;
	}
	else
	{
		theform.showrowid.value = 0;
	}
	if (paramform.showdoi.checked)
	{
		theform.showdoi.value = 1;
	}
	else
	{
		theform.showdoi.value = 0;
	}
	if (paramform.showpii.checked)
	{
		theform.showpii.value = 1;
	}
	else
	{
		theform.showpii.value = 0;
	}
	if (paramform.showpmcid.checked)
	{
		theform.showpmcid.value = 1;
	}
	else
	{
		theform.showpmcid.value = 0;
	}
	if (paramform.showif.checked)
	{
		theform.showif.value = 1;
	}
	else
	{
		theform.showif.value = 0;
	}
	if (paramform.showinter.checked)
	{
		theform.showinter.value = 1;
	}
	else
	{
		theform.showinter.value = 0;
	}
	if (paramform.justinter.checked)
	{
		theform.justinter.value = 1;
	}
	else
	{
		theform.justinter.value = 0;
	}
	if (paramform.higlightfirstlast.checked)
	{
		theform.higlightfirstlast.value = 1;
	}
	else
	{
		theform.higlightfirstlast.value = 0;
	}
	if (paramform.justhigh.checked)
	{
		theform.justhigh.value = 1;
	}
	else
	{
		theform.justhigh.value = 0;
	}
	
	theform.afterdate.value = paramform.afterdate.value;
	theform.stopdate.value = paramform.stopdate.value;
	theform.altsort.value = paramform.altsort.selectedIndex;
	//alert(paramform.afterdate.value);
	//alert(paramform.altsort.selectedIndex);

	//alert(document.parameters.skipdate.checked);

	return true;
}
</script>
<title><?php echo $applicationtitle; ?> - Publication Display Menu</title>
</head>
<body>
<?php
include "header.php";

$dispactionscript = "disppubs.php" ;
$query = "select * from systemconf where rowid = 1";
$result = pg_query_params($mydbh,$query,array());
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$sysconfrow = pg_fetch_array($result);
$currdescript = $sysconfrow['historical_program_alignment'];
$prop1descript = $sysconfrow['proposed_alignment_1'];
$prop2descript = $sysconfrow['proposed_alignment_2'];

?>
<center>
<table width = "90%"><tr>
<td align="left"><h1><?php echo $applicationtitle; ?> - Publication Display Menu</h1>
<form name="myparameters">
<input type="checkbox" checked name="enumerate"> Number References<br><br>
<input type="checkbox" checked name="showdate"> Display Publication Date<br><br>
<input type="checkbox" checked name="showrowid"> Display Publication Internal Rowid (Date must also be selected)<br><br>
<input type="checkbox" checked name="showabstract"> Include Link to PubMed Abstract<br><br>
<input type="checkbox" checked name="showdoi"> If available display DOI<br><br>
<input type="checkbox" checked name="showpii"> If available display PII<br><br>
<input type="checkbox" checked name="showpmcid"> If available display PubMedCentral ID<br><br>
<input type="checkbox" checked name="showif"> Display Current Impact Factor<br><br>
<input type="checkbox" name="justhigh"> Only Pubs with an IF > 9<br><br>
<input type="checkbox" checked name="showinter"> Mark Inter-Institutional Publications with &sect;<br><br>
<input type="checkbox" name="justinter"> Only Display Inter-Institutional Publications<br><br>
<input type="checkbox" name="higlightfirstlast"> Highlight Publications where SKCC Member is First/Senior Author<br><br>
<?php
$repparams = '<input type="hidden" name="enumerate">
<input type="hidden" name="skipdate">
<input type="hidden" name="showrowid">
<input type="hidden" name="afterdate">
<input type="hidden" name="stopdate">
<input type="hidden" name="altsort">
<input type="hidden" name="showabstract">
<input type="hidden" name="showdoi">
<input type="hidden" name="showpii">
<input type="hidden" name="showpmcid">
<input type="hidden" name="justhigh">
<input type="hidden" name="showif">
<input type="hidden" name="showinter">
<input type="hidden" name="justinter">
<input type="hidden" name="higlightfirstlast">';
?>
Only Display Publications On or After (mm/dd/yyyy): <input type="text" name="afterdate" value="01/01/2013"><br><br>
Only Display Publications On or Before (mm/dd/yyyy): <input type="text" name="stopdate"><br><br>
Sort Publications by: <select name="altsort">
<option selected value="0">Pubdate, Authors</option>
<option value="1">Authors, Pubdate</option>
<option value="2">ROWID</option>
<option value="3">Journal, Pubdate</option>
<option value="4">Journal Impact Factor, Pubdate</option>
<option value="5">Journal Impact Factor, Authors</option>
</select>
</form><br>
<HR><b><?php echo $currdescript; ?></b><br />
<br />
<form name="allpubs" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<?php echo $repparams; ?>
<input type="submit" value="Show Entire <?php echo $centerabbrev; ?> Bibliography"><br><br>
</form>
<form name="progpubs" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<?php echo $repparams; ?>
<select name="prog">
<?php 
$cprogquery = 'select * from programs where isactive and iscurrprog order by sortnum ';
$cprogresult = pg_query_params($mydbh,$cprogquery,array());
if (!$cprogresult) {printf (pg_last_error($mydbh)); exit;}
while ($cprogrow = pg_fetch_array($cprogresult))
{
	$thiscode = $cprogrow['program_code'];
	$thisname = $cprogrow['program_name'];
	echo '<option value="' . $thiscode . '">' . $thisname . '[' . $thiscode . "]</option>\n";
}
?>
</select>
<input type="submit" value="Show  <?php echo $centerabbrev; ?> Program Bibliography">
</form>
<form name="authpubs" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<?php 
echo $repparams; 

$authquery = "select * from members order by name" ;
$authresult = pg_query_params($mydbh, $authquery,array());
if (!$authresult) {printf (pg_last_error($mydbh)); exit;}
$numauths = 0;
while($authrow = pg_fetch_array($authresult))
{
	
	$authors[$numauths] = $authrow["name"];
	$authids[$numauths] = $authrow["rowid"];
	$tmpstr = trim($authrow["pmsearch"]);
	$nameparts = explode(" ", $tmpstr);
	$i = 0;
	$tmpstr2 = "";
	$space = "";
	while ($i < (sizeof($nameparts) - 1))
	{
		$tmpstr2 .= $space . $nameparts[$i];
		$i++;
		$space = " ";
	}
	$tmpstr2 .= $space . substr($nameparts[$i],0,1);
	
	$authsrch[$numauths] = $tmpstr2;
	$numauths++;	
}

?>
<select name="author">
<?php
$i = 0;
while ($i < $numauths)
{
	echo '<option value="' . $authsrch[$i] . '">' . $authors[$i] . "</option>\n";
	$i++;
}
?>
</select>
<input type="submit" value="Show  <?php echo $centerabbrev; ?>  Member Bibliography">
</form>
<hr><b><?php echo $prop1descript; ?></b><br />
<br />
<form name="allpubsp1" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<input type="hidden" name="useprop1" value="1">
<?php echo $repparams; ?>
<input type="submit" value="Show Entire  <?php echo $centerabbrev; ?>  Bibliography"><br><br>
</form>
<form name="progpubs1" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<input type="hidden" name="useprop1" value="1">
<?php echo $repparams; ?>
<select name="prog">
<?php 
$p1progquery = 'select * from programs where isactive and isaltprog1 order by sortnum ';
$p1progresult = pg_query_params($mydbh,$p1progquery,array());
if (!$p1progresult) {printf (pg_last_error($mydbh)); exit;}
while ($p1progrow = pg_fetch_array($p1progresult))
{
	$thiscode = $p1progrow['program_code'];
	$thisname = $p1progrow['program_name'];
	echo '<option value="' . $thiscode . '">' . $thisname . '[' . $thiscode . "]</option>\n";
}
?>
</select>
<input type="submit" value="Show  <?php echo $centerabbrev; ?> Program Bibliography">
</form>
<form name="authpubs1" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<input type="hidden" name="useprop1" value="1">
<?php 
echo $repparams; 

?>
<select name="author">
<?php
$i = 0;
while ($i < $numauths)
{
	echo '<option value="' . $authsrch[$i] . '">' . $authors[$i] . "</option>\n";
	$i++;
}
?>
</select>
<input type="submit" value="Show  <?php echo $centerabbrev; ?>  Member Bibliography">
</form>
<hr><b><?php echo $prop2descript; ?></b><br />
<br />
<form name="allpubsp2" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<input type="hidden" name="useprop2" value="1">
<?php echo $repparams; ?>
<input type="submit" value="Show Entire  <?php echo $centerabbrev; ?>  Bibliography"><br><br>
</form>
<form name="progpubs2" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<input type="hidden" name="useprop2" value="1">
<?php echo $repparams; ?>
<select name="prog">
<?php 
$p1progquery = 'select * from programs where isactive and isaltprog2 order by sortnum ';
$p1progresult = pg_query_params($mydbh,$p1progquery,array());
if (!$p1progresult) {printf (pg_last_error($mydbh)); exit;}
while ($p1progrow = pg_fetch_array($p1progresult))
{
	$thiscode = $p1progrow['program_code'];
	$thisname = $p1progrow['program_name'];
	echo '<option value="' . $thiscode . '">' . $thisname . '[' . $thiscode . "]</option>\n";
}
?>
</select>
<input type="submit" value="Show  <?php echo $centerabbrev; ?>  Program Bibliography">
</form>
<form name="authpubs2" method="get" action="<?php echo $dispactionscript; ?>" target="_blank"  onSubmit="copyparameters(this);">
<input type="hidden" name="useprop2" value="1">
<?php 
echo $repparams; 

?>
<select name="author">
<?php
$i = 0;
while ($i < $numauths)
{
	echo '<option value="' . $authsrch[$i] . '">' . $authors[$i] . "</option>\n";
	$i++;
}
?>
</select>
<input type="submit" value="Show  <?php echo $centerabbrev; ?> v Member Bibliography">
</form>
<hr>
<br />
<a href="journalsummary.php">High Impact Journal Summary (ignores program structure completely)</a><br />
<br />
</td>
</tr>
</form>
</table>
<?php
pg_close($mydbh);
include("footer.php");
?>
</body>
</html>
