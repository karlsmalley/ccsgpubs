<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";

$curpage = 0;
$qstring = "";
$pubrowid = 0;
$qrowid = 6500;
$qrowfunct = "ge";
$qstring = "";
$qfield = "author_prog";

$drexelpub = null;
$limrpub = null;
$intraconsortium = null;
$notifyonupdate = null;
$disptext = null;
$author_prog = null;
$author_prog_prop_1 = null;
$author_prog_prop_2 = null;

if (isset($_POST['disptext']) && strlen(trim($_POST['disptext'])) > 0)
{
	$disptext = trim($_POST['disptext']);
}
if (isset($_POST['author_prog']) && strlen(trim($_POST['author_prog'])) > 0)
{
	$author_prog = trim($_POST['author_prog']);
}
if (isset($_POST['author_prog_prop_1']) && strlen(trim($_POST['author_prog_prop_1'])) > 0)
{
	$author_prog_prop_1 = trim($_POST['author_prog_prop_1']);
}
if (isset($_POST['author_prog_prop_2']) && strlen(trim($_POST['author_prog_prop_2'])) > 0)
{
	$author_prog_prop_2 = trim($_POST['author_prog_prop_2']);
}


if (isset($_POST['drexelpub']))
{
	$drexelpub = 't';
}
if (isset($_POST['limrpub']))
{
	$limrpub = 't';
}
if (isset($_POST['intraconsortium']))
{
	$intraconsortium = 't';
}
if (isset($_POST['notifyonupdate']))
{
	$notifyonupdate = 't';
}

if (isset($_POST['curpage']))
{
	$curpage = $_POST['curpage']*1;
}
if (isset($_POST['qstring']))
{
	$qstring = $_POST['qstring'];
}
if (isset($_POST["curpage"]))
{
	$curpage = $_POST["curpage"];
}
if (isset($_POST["qrowid"]))
{
	$qrowid = $_POST["qrowid"] * 1;
}
if (isset($_POST["qrowfunct"]))
{
	$qrowfunct = trim($_POST["qrowfunct"]);
}

if (isset($_POST["qfield"]))
{
	$qfield  = trim($_POST["qfield"]);
}

if (isset($_POST['pubrowid']))
{
	$pubrowid = $_POST['pubrowid']*1;
}
if ($pubrowid == 0)
{
?> 
	<font face=arial size=3 color=red ><b>Publication Row ID Not found -- please use these pages in the proper order</b></font><br><br>
	</body></html>
<?php
	exit();
}
?>
<html>
<head>
<title><?php echo $applicationtitle; ?> - Save Publications Updates</title>
<?php include "header.php"; ?>
<form name="returntolist" method="post" action="modpublications.php">
<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
<input type="hidden" name="qrowid" value="<?php echo $qrowid ?>">
<input type="hidden" name="qfield" value="<?php echo $qfield ?>">
<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
<input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct ?>">
<?php


$query = 'update ccsgpublications set drexelpub = $1, limrpub=$2 ,intraconsortium=$3, notifyonupdate=$4, disptext=$5, author_prog=$6, '; 
$query .= 'author_prog_prop_1=$7, author_prog_prop_2=$8  where rowid = $9';
//echo "<br />query: $query<br>parameter_array<pre />";
//var_dump(array($drexelpub, $limrpub, $intraconsortium, $notifyonupdate, $disptext, $author_prog, $author_prog_prop_1, $author_prog_prop_2,$pubrowid));
//echo '</pre><br />';
$result = pg_query_params($mydbh, $query,array($drexelpub, $limrpub, $intraconsortium, $notifyonupdate, $disptext, $author_prog, $author_prog_prop_1, $author_prog_prop_2,$pubrowid ));
if (!$result) {printf (pg_last_error($mydbh)); exit;}
echo '<br /><br /><font face="Arial, Helvetica, sans-serif" color="#FF0000" >The Publication Updates Have Been Saved.<br><br>';
echo '</font><a href="javascript:returntolist.submit();">Return to Publication Listing</a>';	
include('footer.php');
pg_close($mydbh);
?>

</body>
</html>
