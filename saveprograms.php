<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";

$curpage = 0;
$qstring = "";
$prog_id = 0;

$program_name = null;
$program_code =  null;
$iscurrprog =  null;
$isaltprog1 =  null;
$isaltprog2 =  null;
$isactive =  null;
$sortnum =  null;


if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = trim($_POST['qstring']);
}
if (isset($_POST['prog_id']))
{
	$prog_id = $_POST['prog_id']*1;
}
if (isset($_POST['program_name']) and strlen(trim($_POST['program_name'])) > 0)
{
	$program_name = trim($_POST['program_name']);
}
if (isset($_POST['program_code']) and strlen(trim($_POST['program_code'])) > 0)
{
	$program_code = trim($_POST['program_code']);
}
if (isset($_POST['iscurrprog']) and strlen(trim($_POST['iscurrprog'])) > 0)
{
	$iscurrprog = trim($_POST['iscurrprog']);
}
if (isset($_POST['isaltprog1']) and strlen(trim($_POST['isaltprog1'])) > 0)
{
	$isaltprog1 = trim($_POST['isaltprog1']);
}
if (isset($_POST['isaltprog2']) and strlen(trim($_POST['isaltprog2'])) > 0)
{
	$isaltprog2 = trim($_POST['isaltprog2']);
}
if (isset($_POST['isactive']) and strlen(trim($_POST['isactive'])) > 0)
{
	$isactive = trim($_POST['isactive']);
}
if (isset($_POST['sortnum']) and strlen(trim($_POST['sortnum'])) > 0)
{
	$sortnum = trim($_POST['sortnum']);
}


if ($prog_id == 0)
{
?> 
	</head>
	<body>
	<font face=arial size=3 color=red ><b>Program ID Not found -- please use these pages in the proper order</b></font><br><br>
	</body></html>
<?php
	exit();
}
if ($prog_id != -99)
{
	$query = 'select count(*) from programs where rowid = $1';
	$result = pg_query_params($mydbh, $query,array($prog_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);
	if (($row[0]*1) < 1)
	{
?> 
	</head>
	<body>
	<font face=arial size=3 color=red ><b>Could Not Find Program Record With Supplied Program ID To Update -- Program Update Aborted!!</b></font><br><br>
	</body></html>
<?php
		exit();
	}
}

?>
<html>
<head>
<title><?php echo $applicationtitle; ?> - Save Program Updates</title>
<?php include "header.php"; ?>
<form name="returntolist" action="modprograms.php" method="POST">
<input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
<input type="hidden" name="curpage" value="<?php echo $curpage; ?>">
</form>

<?php



if ($prog_id == -99)
{
	$query = 'INSERT into programs (program_name, program_code, iscurrprog, isaltprog1, isaltprog2, isactive, sortnum) values ($1,$2,$3,$4,$5,$6,$7)';
	$result = pg_query_params($mydbh, $query,array($program_name, $program_code, $iscurrprog, $isaltprog1, $isaltprog2, $isactive, $sortnum));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The Research Program Has Been Added.<br><br>';
}
else
{
	
   
	$query = 'update programs set program_name = $1, program_code=$2 ,iscurrprog=$3, isaltprog1=$4, isaltprog2=$5, isactive=$6, sortnum=$7  '; 
	$query .= ' where rowid = $8';
	//echo $query . "<br>";
	$result = pg_query_params($mydbh, $query,array($program_name, $program_code, $iscurrprog, $isaltprog1, $isaltprog2, $isactive, $sortnum, $prog_id));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The Research Program Updates Have Been Saved.<br><br>';
}
echo '</font><a href="javascript:returntolist.submit();">Return to Program Listing</a>';	
include('footer.php');
pg_close($mydbh);
?>

</body>
</html>
