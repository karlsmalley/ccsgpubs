<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
include "header.php";
$i = 0;
$saveidcnt = 0;
$memberid = 0;

if (isset($_POST["memberid"]))
{
	$memberid = $_POST["memberid"]*1;
}
if ($memberid == 0)
{
	echo "Unable to Find Member ID, will not process any IDs";
	include('footer.php');
	pg_close($mydbh);
	exit();
}
$memquery = "select name from members where rowid = $1";
$memresult = pg_query_params($mydbh, $memquery,array($memberid));
if (!$memresult) {printf (pg_last_error($mydbh)); exit;}
$memrow = pg_fetch_array($memresult);
$memname = $memrow['name'];
while(isset($_POST["uid_" . $i]))
{
	$dosaveid = 0;
	$dorejectid = 0;
	$nosavebecause = "- NOT REQUESTED";
	if(isset($_POST["save_" . $i]))
	{
		$decision = $_POST["save_" . $i];
		$thisuid = $_POST["uid_" . $i]*1;
		if ($decision == "save")
		{
			$dosaveid = 1;
			$dorejectid = 0;
		}	
		if ($decision == "reject")
		{
			$dosaveid = 0;
			$dorejectid = 1;
		}
		if ($dorejectid)
		{
			$query = 'select count(*) from rejpubs where pubmedid = $1 and (memberid is null or memberid = $2)';
//			echo "<br>query$query<br>\n";
			$result = pg_query_params($mydbh, $query,array($thisuid,$memberid));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
			$row = pg_fetch_array($result);
			if($row[0] > 0)
			{
				$dorejectid = 0;
				$nosavebecause = "- ALREADY IN REJECTION TABLE";
			}
			else
			{
				$query = 'insert into rejpubs (pubmedid,memberid,memname) values ($1,$2, $3) ';
	//			echo "<br>query$query<br>\n";
				$result = pg_query_params($mydbh, $query,array($thisuid,$memberid,$memname));
				if (!$result) {printf (pg_last_error($mydbh)); exit;}
				$nosavebecause = "- ADDED TO REJECTION TABLE";
			}
		
		}
		if ($dosaveid)
		{
			$query = 'select count(*) from ccsgpublications where pubmedid = $1';
			//echo "<br>query$query<br>\n";
			$result = pg_query_params($mydbh, $query,array($thisuid));
			if (!$result) {printf (pg_last_error(mydbh)); exit;}
			$row = pg_fetch_array($result);
			if($row[0] > 0)
			{
				$dosaveid = 0;
				$nosavebecause = "- ALREADY IN PUBLICATIONS TABLE";
			}
		}
	}
	if (!$dosaveid)
	{
		echo "Will NOT Save UID: $i " . $_POST["uid_" . $i] . "$nosavebecause<br>";
	}
	else
	{
		echo "Will Save UID: $i " . $_POST["uid_" . $i] . "<br>";
		$saveuids[$saveidcnt] = $_POST["uid_" . $i];
		$saveidcnt++;
	}
	$i++;
}

flush();
ob_flush();
$getfirstrowid = 1;
$firstrowid = 0;
$i = 0;
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

