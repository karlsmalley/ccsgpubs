<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
//exit();
$curpage = 0;
$qstring = "";
$justactives = 0;
$member_id = 0;

$name = null;
$mindate =  null;
$maxdate =  null;
$pmsearch =  null;
$pmsearch2 =  null;
$progs =  null;
$alt_progs_1 =  null;
$alt_progs_2 =  null;
$skipme =  null;
$isactive =  null;



if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = trim($_POST['qstring']);
}
if (isset($_POST['justactives']))
{
	$justactives = $_POST['justactives']*1;
}
if (isset($_POST['member_id']))
{
	$member_id = $_POST['member_id']*1;
}

if (isset($_POST['name']) and strlen(trim($_POST['name'])) > 0)
{
	$name = trim($_POST['name']);
}
if (isset($_POST['mindate']) and strlen(trim($_POST['mindate'])) > 0)
{
	$mindate = trim($_POST['mindate']);
}
if (isset($_POST['maxdate']) and strlen(trim($_POST['maxdate'])) > 0)
{
	$maxdate = trim($_POST['maxdate']);
}
if (isset($_POST['pmsearch']) and strlen(trim($_POST['pmsearch'])) > 0)
{
	$pmsearch = trim($_POST['pmsearch']);
}
if (isset($_POST['pmsearch2']) and strlen(trim($_POST['pmsearch2'])) > 0)
{
	$pmsearch2 = trim($_POST['pmsearch2']);
}
if (isset($_POST['skipme']) and strlen(trim($_POST['skipme'])) > 0)
{
	$skipme = trim($_POST['skipme']);
}
if (isset($_POST['isactive']) and strlen(trim($_POST['isactive'])) > 0)
{
	$isactive = trim($_POST['isactive']);
}

if ($member_id == 0)
{
?> 
	<font face=arial size=3 color=red ><b>Member ID Not found -- please use these pages in the proper order</b></font><br><br>
	</body></html>
<?php
	exit();
}

$pquery = "select * from programs where isactive order by sortnum ";
$presult = pg_query_params($mydbh,$pquery,array());
if (!$presult) {printf (pg_last_error($mydbh)); exit;}
$cpdelim = "";
$ap1delim = "";
$ap2delim = "";
$progs = "";
$alt_progs_1 =  "";
$alt_progs_2 =  "";

while($prow = pg_fetch_array($presult) )
{
	$thisprog = $prow['program_code'];
	if (isset($_POST["currprog_" . $thisprog]))
	{
		$progs .= $cpdelim . $thisprog;
		$cpdelim = '|';
	}
	if (isset($_POST["altprog1_" . $thisprog]))
	{
		$alt_progs_1 .= $ap1delim . $thisprog;
		$ap1delim = '|';
	}
	if (isset($_POST["altprog2_" . $thisprog]))
	{
		$alt_progs_2 .= $ap2delim . $thisprog;
		$ap2delim = '|';
	}
}

?>
<html>
<head>
<title><?php echo $applicationtitle; ?> - Save Member Updates</title>
<?php include "header.php"; ?>
<form name="returntolist" action="modmembers.php" method="POST">
<input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
<input type="hidden" name="justactives" value="<?php echo $justactives; ?>">
<input type="hidden" name="curpage" value="<?php echo $curpage; ?>">
</form>

<?php



if ($member_id == -99)
{
	$query = 'INSERT into members (name, mindate, maxdate, pmsearch, pmsearch2, progs, alt_progs_1, alt_progs_2, skipme, isactive) values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)';
	$result = pg_query_params($mydbh, $query,array($name, $mindate, $maxdate, $pmsearch, $pmsearch2, $progs, $alt_progs_1, $alt_progs_2, $skipme, $isactive));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The Center Member Has Been Added.<br><br>';
}
else
{
	
   
	$query = 'update members set name = $1, mindate=$2 ,maxdate=$3, pmsearch=$4, pmsearch2=$5, progs=$6, alt_progs_1=$7, alt_progs_2=$8, skipme=$9, isactive=$10 '; 
	$query .= ' where rowid = $11';
	$result = pg_query_params($mydbh, $query,array($name, $mindate, $maxdate, $pmsearch, $pmsearch2, $progs, $alt_progs_1, $alt_progs_2, $skipme, $isactive, $member_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The Center Member Updates Have Been Saved.<br><br>';
}
echo '</font><a href="javascript:returntolist.submit();">Return to Member Listing</a>';	
include('footer.php');
pg_close($mydbh);
?>

</body>
</html>
