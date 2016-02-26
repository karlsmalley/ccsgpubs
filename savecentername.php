<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";

$lastmod = null;
$centername = null;
$centerabbrev = null;
$consortium = null;
$historical_program_alignment = null;
$proposed_alignment_1 = null;
$proposed_alignment_2 = null;


if (isset($_POST['lastmod']) and strlen(trim($_POST['lastmod'])) > 0)
{
	$lastmod = trim($_POST['lastmod']);
}
if (isset($_POST['centername']) and strlen(trim($_POST['centername'])) > 0)
{
	$centername = trim($_POST['centername']);
}
if (isset($_POST['centerabbrev']) and strlen(trim($_POST['centerabbrev'])) > 0)
{
	$centerabbrev = trim($_POST['centerabbrev']);
}
if (isset($_POST['historical_program_alignment']) and strlen(trim($_POST['historical_program_alignment'])) > 0)
{
	$historical_program_alignment = trim($_POST['historical_program_alignment']);
}
if (isset($_POST['proposed_alignment_1']) and strlen(trim($_POST['proposed_alignment_1'])) > 0)
{
	$proposed_alignment_1 = trim($_POST['proposed_alignment_1']);
}
if (isset($_POST['proposed_alignment_2']) and strlen(trim($_POST['proposed_alignment_2'])) > 0)
{
	$proposed_alignment_2 = trim($_POST['proposed_alignment_2']);
}
if (isset($_POST['consortium']) and strlen(trim($_POST['consortium'])) > 0)
{
	$consortium = trim($_POST['consortium']);
}

?>
<html>
<head>
<title><?php echo $applicationtitle; ?> - Save User Updates</title>
<?php 
include "header.php"; 

   
$query = 'update systemconf  set lastmod = $1, centername=$2 ,centerabbrev=$3, historical_program_alignment=$4, proposed_alignment_1=$5,  proposed_alignment_2=$6, consortium = $7 ';

$query .= '  where rowid=1'; 
	//echo $query . "<br>";
$result = pg_query_params($mydbh, $query,array($lastmod, $centername, $centerabbrev, $historical_program_alignment,$proposed_alignment_1,$proposed_alignment_2,$consortium));
if (!$result) {printf (pg_last_error($mydbh)); exit;}
echo '<font face="Arial, Helvetica, sans-serif" color="#FF0000" >The Center Name, etc. Have Been Saved.<br><br>';
echo '</font><a href="./menu.php">Return to Main Menu</a>';	


include('footer.php');
pg_close($mydbh);
?>

</body>
</html>
