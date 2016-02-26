<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
include "header.php";


$i = 0;
$saveidcnt = 0;


$uidlist = $_POST["uidlist"];
$possuids = explode("\n",$uidlist);
$uidlist = "";
if (isset($_POST['uidlist']) && strlen(trim($_POST['uidlist'])) > 0)
{
	$uidlist = trim($_POST['uidlist']);
}
$possuids = explode("\n",$uidlist);
if (sizeof($possuids) == 0)
{
?> 
	<font face=arial size=3 color=red ><b>PubMed ID List Not Found Or Empty  -- Publications Table Not Modified</b></font><br><br>
	</body></html>
<?php
	include('footer.php');
	pg_close($mydbh);
	exit();
}

$saveidcnt = 0;
foreach ($possuids as $thisuid)
{
	$dosaveid = 0;
	$query = 'select count(*) from ccsgpublications where pubmedid = $1';
	$result = pg_query_params($mydbh, $query,array(trim($thisuid)));
	if (!$result) {printf (pg_last_error($mydbh)); exit;}
	$row = pg_fetch_array($result);
	if($row[0] > 0)
	{
			$dosaveid = 0;
			echo "$thisuid IS ALREADY IN PUBLICATIONS DATABASE<br />";
	}
	else
	{
		$saveuids[$saveidcnt] = $thisuid;
		$saveidcnt++;
	}
}
echo "saveidcnt: $saveidcnt<br>";


flush();
ob_flush();

$i = 0;
$getfirstrowid = 1;

while ($i < $saveidcnt)
{
	$pubid = $saveuids[$i];
	
	$year = null;
	$regauth=null;
	$fullauth=null;
	$authandaffil=null;
	$Affiliations=null;
	$journal_issn = null;
	$dispdate = null;
	$pmcid = null;
	$doi = null;
	$pii = null;
	$ArticleTitle = null;
	$MedlinePgn = null;
	$Volume = null;
	$Issue = null;
	$PublicationStatus = null;
	$JournalTitle = null;
	$MedlineTA = null;
	$so = null;
	$orig_xml = null;
	$updated_xml = null;

	$isjournal = 0;
	include "parse-pub.php";
	if ($isjournal != 0)
	{
		$query = "INSERT into ccsgpublications (";
		$query .= "pubyear, pubnum,pubmedid,disptext,disppubdate,pubdate,journal,authors,authorsfull,title,volume,issue,";
		$query .= "journal_issn,pages,pubstatus,sourcestring,pmcid,afilliations,orig_xml,updated_xml,pii,doi,journal_full_title) ";
		$query .= "	values(";
		$query .= "$1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23)";
		$valarray[0] = $year;
		$valarray[1] = $i+1;
		$valarray[2] = $pubid;
		$valarray[3] = $disptext;
		$valarray[4] = $dispdate;
		$valarray[5] = $dateofpub;
		$valarray[6] = $MedlineTA;
		$valarray[7] = $regauth;
		$valarray[8] = $fullauth;
		$valarray[9] = $ArticleTitle;
		$valarray[10] = $Volume;
		$valarray[11] = $Issue;
		$valarray[12] = $journal_issn;
		$valarray[13] = $MedlinePgn;
		$valarray[14] = $PublicationStatus;
		$valarray[15] = $so;
		$valarray[16] = $pmcid;
		$valarray[17] = $Affiliations;
		$valarray[18] = $orig_xml;
		$valarray[19] = $updated_xml;
		$valarray[20] = $pii;
		$valarray[21] = $doi;
		$valarray[22] = $JournalTitle;

	

		// echo "<br>query:$query<br>\n";	
		// echo "<br /><pre>";
		// var_dump($valarray);
		// echo "</pre><br />";
	
		$result = pg_query_params($mydbh, $query, $valarray);
		if (!$result) {printf (pg_ErrorMessage()); exit;}
		if ($getfirstrowid)
		{
			$frowquery = "select currval('ccsgpublications_rowid_seq')";
			$frowresult = pg_query_params($mydbh,$frowquery,array());
			if (!$frowresult) {printf (pg_ErrorMessage()); exit;}
			$frowrow = pg_fetch_array($frowresult);
			$firstrowid = $frowrow[0];
			$getfirstrowid = 0;
		}
		if (strlen(trim($journal_issn)) > 0)
		{
			$issnquery = "select count(*) from imp_fac_link where publication_issn = $1";
			$issnresult = pg_query_params($mydbh, $issnquery,array($journal_issn));
			if (!$issnresult) {printf (pg_ErrorMessage()); exit;}
			$issnrow = pg_fetch_array($issnresult);
			if ($issnrow[0] == 0)
			{
				echo '<br /><font color="red">ISSN: ' . "$journal_issn   Journal: .  $MedlineTA   NOT FOUND IN IMP_FAC_LINK</font><br />";
				$linkquery = "insert into imp_fac_link (publication_issn,impact_factor_issn) values ($1,$1)";
				//echo "linkquery: $linkquery<br />";
				$linkresult = pg_query_params($mydbh, $linkquery,array($journal_issn));
				if (!$linkresult) {printf (pg_ErrorMessage()); exit;}
				$ifquery = "insert into imp_factors (journal_name,issn,";
				$ifquery .= "impact_factor_13_14,impact_factor_12,impact_factor_11,impact_factor_10,impact_factor_9,abbrev_name) ";
				$ifquery .= "values ($1,$2,-99,-99,-99,-99,-99,$3)";
				//echo "ifquery: $ifquery<br />";
				$ifresult = pg_query_params($mydbh, $ifquery,array($MedlineTA,$journal_issn,strtoupper($MedlineTA)));
				if (!$ifresult) {printf (pg_ErrorMessage()); exit;}
				echo '<br /><font color="red">A LINK RECORD AND IMPACT FACTOR RECORD HAS BEEN CREATED.</font><br />';
			}
			else
			{
				echo "<br />A LINK RECORD AND MPACT FACTOR RECORD EXISTS FOR ISSN: $journal_issn   Journal: $MedlineTA<br />";
			}
		}
		else
		{
			echo '<br /><font color="red">No ISSN for ' . "  Journal: .  $MedlineTA   NOT FOUND IN IMP_FAC_LINK</font><br />";
		}
	}
	else
	{
		echo '<br /><font color="red">Publication Not Added Because It Is Not A Journal Article.<br />';
	}
	flush();
	ob_flush();
	$i++;
}
if ($saveidcnt > 0)
{
?>
	<br  />
	<form name="modnew" method="POST" action="modpublications.php">
	<input type="hidden" name="qrowid" value="<?php echo $firstrowid; ?>">
	<input type="hidden" name="qrowfunct" value="ge">
	<input type="submit" value="Modify Just Added Publications (Author-Pogram Alignments, etc. )">
	</form>
<?php
}

echo '<br /><br /><a href="menu.php">Return TO Main Menu</a><br /><br />';
include('footer.php');
pg_close($mydbh);
?>

