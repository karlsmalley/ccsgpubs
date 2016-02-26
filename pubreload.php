<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
include "header.php";



$nummonths = 6;
if (isset($_POST['nummonths']))
{
	$nummonths = $_POST['nummonths']*1;
}
if ($nummonths == 0)
{
	$nummonths = 6;	
}



$backquery = "update ccsgpublications set disptextbak = disptext";
$resultback = pg_query_params($mydbh,$backquery,array());

$squery = "select * from ccsgpublications where pubdate > (current_date - interval '$nummonths Months') order by rowid";
//$squery = "select * from ccsgpublications where rowid = 6645 or rowid = 7001 or rowid = 6995 or rowid = 8012 or rowid = 6216";

$sresult = pg_query($mydbh,$squery);
if (!$sresult) {printf (pg_last_error($mydbh)); exit;}

while ($srow = pg_fetch_array($sresult))
{
	$uid = $srow["pubmedid"];
	$rowid = $srow["rowid"];
	$notifyonupdate =  $srow["notifyonupdate"];

	flush();
	ob_flush();

	$pubid = $uid;

	$foundpubok = 1;
	echo "Reloading - PUBMEDID: $pubid  . . ";
	include "parse-pub.php";
	
	$query = "update ccsgpublications set ";
	$query .= "pubyear = $1, ";
	$query .= "disptext = $2, ";
	$query .= "disppubdate = $3, ";
	$query .= "pubdate = $4, ";
	$query .= "journal = $5, ";
	$query .= "authors = $6, ";
	$query .= "authorsfull = $7, ";
	$query .= "title = $8, ";
	$query .= "pmcid = $9, ";
	$query .= "pubstatus = $10, ";
	$query .= "volume = $11, ";
	$query .= "issue = $12, ";
	$query .= "journal_issn = $13, ";
	$query .= "pages = $14, ";
	$query .= "updated_xml = $15, ";
	$query .= "doi = $16, ";
	$query .= "pii = $17, ";
	$query .= "afilliations = $18, "; 
	$query .= "sourcestring = $19 ";
	$query .= " where pubmedid = $20 ";
	
	$result = pg_query_params($mydbh, $query,array($year,$disptext,$dispdate,$dateofpub,$MedlineTA,$regauth,$fullauth,$ArticleTitle,$pmcid,$PublicationStatus,$Volume,$Issue,$journal_issn,$MedlinePgn,$updated_xml,$doi,$pii,$Affiliations, $so,$uid));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}

	if (!$foundpubok)
	{
		echo "<font color=red>Unable To Find Data For PubMedID: $uid   ROWID: $rowid</font><br />";
	}
	else
	{
		echo "Updated Publication Data For PubMedID: $uid   ROWID: $rowid<br />";
		if ($notifyonupdate == 't' )
		{
			echo "<font color=red>Publication Data Update for Publication with ROWID: $rowid and marked NOTIFY ON UPDATE</font><br />";
		}
	}
}
echo '<br /><br /><a href="menu.php">Return To Main Menu</a><br /<br />';
pg_close($mydbh);
include("footer.php");
?>

