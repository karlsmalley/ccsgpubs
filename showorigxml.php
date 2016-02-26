<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
?>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $applicationtitle; ?> Update Publication Entry</title>
<SCRIPT LANGUAGE="Javascript">
function usesuggestion()
{
	document.saveupdates.author_prog.value = document.saveupdates.scnames.value;
	document.saveupdates.author_prog_prop_1.value = document.saveupdates.sp1names.value;
	document.saveupdates.author_prog_prop_2.value = document.saveupdates.sp2names.value;
	
}
</SCRIPT>
<?php 
include "header.php";

$curpage = 0;
$qstring = "";
$pubrowid = 0;
$qrowid = 0;
$qrowfunct = "ge";
$qstring = "";
$qfield = "author_prog";

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
	

$booltypes[0] = "";
$booltypes[1] = "YES";
$booltypes[2] = "NO";
$boolvals[0] = "";
$boolvals[1] = "t";
$boolvals[2] = "f";

$query = 'select * from ccsgpublications where rowid = $1';
$result = pg_query_params($mydbh, $query,array($pubrowid));
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$row = pg_fetch_array($result);

/*
pubyear bigint,
pubmedid bigint,
disptext text,
disppubdate text,
pubdate date,
journal text,
authors text,
authorsfull text,
title text,
volume text,
issue text,
pages text,
sourcestring text,
author_prog text,
author_prog_prop_1 text,
author_prog_prop_2 text,
omitfrom text,
report_year smallint,
pmcid text,
pubstatus text,
journal_issn text,
doi text,
intraconsortium boolean,
rowid serial NOT NULL,
disptextbak text,
notifyonupdate boolean,
drexelpub boolean,
limrpub boolean,
*/


$pubyear  =  $row["pubyear"];
$pubmedid  =  $row["pubmedid"];
$disptext  =  $row["disptext"];
$disppubdate  =  $row["disppubdate"];
$pubdate  =  $row["pubdate"];
$journal  =  $row["journal"];
$authors  =  $row["authors"];
$authorsfull  =  $row["authorsfull"];
$title  =  $row["title"];
$volume  =  $row["volume"];
$issue  =  $row["issue"];
$pages  =  $row["pages"];
$sourcestring  =  $row["sourcestring"];
$author_prog  =  $row["author_prog"];
$author_prog_prop_1  =  $row["author_prog_prop_1"];
$author_prog_prop_2  =  $row["author_prog_prop_2"];
$omitfrom  =  $row["omitfrom"];
$report_year  =  $row["report_year"];
$pmcid  =  $row["pmcid"];
$pubstatus  =  $row["pubstatus"];
$journal_issn  =  $row["journal_issn"];
$doi  =  $row["doi"];
$intraconsortium  =  $row["intraconsortium"];
$rowid  =  $row["rowid"];
$disptextbak  =  $row["disptextbak"];
$notifyonupdate  =  $row["notifyonupdate"];
$drexelpub  =  $row["drexelpub"];
$limrpub  =  $row["limrpub"];
$afilliations  = $row["afilliations"];
$orig_xml  = $row["orig_xml"];
$updated_xml  = $row["updated_xml"];
?>
<table width="100%"><tr><td align="center">
	<form name="showorigxml" method="post" action="showorigxml.php" target="_blank">
	<input type="hidden" name="pubrowid" value="<?php echo $pubrowid ?>">
	</form>
	<form name="showupdxml" method="post" action="showupdxml.php" target="_blank">
	<input type="hidden" name="pubrowid" value="<?php echo $pubrowid ?>">
	</form>
	
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	</form>

	<form name="returntolist" method="post" action="modpublications.php">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="qrowid" value="<?php echo $qrowid ?>">
	<input type="hidden" name="qfield" value="<?php echo $qfield ?>">
	<input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct ?>">
	
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	</form>
	<form name="saveupdates" method="post" action="savepublications.php">
	<input type="hidden" name="qrowid" value="<?php echo $qrowid ?>">
	<input type="hidden" name="qfield" value="<?php echo $qfield ?>">
	<input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct ?>">
	<input type="hidden" name="qstring" value="<?php echo $qstring ?>">
	<input type="hidden" name="curpage" value="<?php echo $curpage ?>">
	<input type="hidden" name="pubrowid" value="<?php echo $pubrowid ?>">
				<table cellpadding="10">
				<tr>
						<td align="center" colspan="3"><font face="Arial, Helvetica, sans-serif"><b>Read Only Fields - Data That Was Saved In The Compilation Of The DisplayText Field (Which Is The Primary OutPut Of The Reports).</td></tr>
			<tr>
				<td valign="top" align="left">RowID: <?php echo $rowid; ?></td>
				<td  valign="top" align="left">PubMed ID: <?php echo $pubmedid; ?></td>
				<td  valign="top" align="left">PubMed Central ID <?php echo $pmcid; ?></td>
			</tr>
			<tr>
				<td valign="top" align="left">Pubyear: <?php echo $pubyear; ?></td>
				<td valign="top" align="left">PubDate (used for queries): <?php echo $pubdate; ?></td>
				<td valign="top" align="left">Disp PubDate (displayed in publication): <?php echo $disppubdate; ?></td>
			</tr>
			<tr>
				<td valign="top" align="left">Journal ISSN: <?php echo $journal_issn; ?></td>
				<td  valign="top" align="left">DOI: <?php echo $doi; ?></td>
				<td valign="top" align="left">Status <?php echo $pubstatus; ?></td>
			</tr>
			<tr><td  valign="top"  align="left">Journal:</td><td  valign="top" colspan="2"><?php echo $journal; ?></td></tr>
			<tr>
				<td valign="top" align="left">Volume: <?php echo $volume; ?></td>
				<td  valign="top" align="left">Issue: <?php echo $issue; ?></td>
				<td valign="top" align="left">Pages <?php echo $pages; ?></td>
			</tr>
			<tr><td  valign="top"  align="left">Source String:</td><td  valign="top" colspan="2"><?php echo $sourcestring; ?></td></tr>

			<tr><td  valign="top"  align="left">Authors (as retrieved from PubMed): </td><td  valign="top" colspan="2"><?php echo $authors; ?></td></tr>
			<tr><td  valign="top" align="left" >Authors (Full names if available): </td><td  valign="top" colspan="2"><?php echo $authorsfull; ?></td></tr>
			<tr><td  valign="top" align="left">Affiliations (if avail. delimited with |): </td><td  valign="top" colspan="2"><?php echo $afilliations; ?></td></tr>
			<tr><td  valign="top" align="left">Title: </td><td  valign="top" colspan="2"><?php echo $title; ?></td></tr>
			<tr><td  valign="top" align="left" colspan="3">DispTextBak (backup of Display Text before update): </td></tr>
			<tr><td  valign="top" colspan="3"><?php echo $disptextbak; ?></td></tr>
				<tr><td align="center" colspan="3"><font face="Arial, Helvetica, sans-serif"><hr /><b>Editable Data</b><hr /></td></tr>
<?php
$icchecked = "";
$dpchecked = "";
$lpchecked = "";
$nuchecked = "";
if ($intraconsortium == 't')
{
	$icchecked = " checked ";
}
if ($drexelpub == 't')
{
	$dpchecked = " checked ";
}
if ($limrpub == 't')
{
	$lpchecked = " checked ";
}
if ($notifyonupdate == 't')
{
	$nuchecked = " checked ";
}
?>
			<tr>
				<td  valign="top" align="left">IntraConsortium:<input type="checkbox" name="intraconsortium" <?php echo $icchecked; ?> ></td>
				<td  valign="top" align="left">Drexel Pub: <input type="checkbox" name="drexelpub" <?php echo $dpchecked; ?> ></td>
				<td  valign="top" align="left">LIMR Pub: <input type="checkbox" name="limrpub" <?php echo $lpchecked; ?> ></td>
				
			</tr>
			<tr>
				<td  valign="top" align="left" colspan="3">Notify on Update (if you made manual updates to Display Text, after an update you may need to reapply manual updates). After an Update run a list of "Notify On Update" RowIDs will be listed: <input type="checkbox" name="notifyonupdate" <?php echo $nuchecked; ?> ></td>
			</tr>

			<tr><td  valign="top" align="left" colspan="3">Display Text (before CAuthor Marking, addition of Impact Factor, PubMed ID, PubMed Central ID, etc.: </td></tr>
		<tr><td  valign="top" align="left" colspan="3"><textarea name = "disptext" rows="6" cols="100"><?php echo $disptext; ?></textarea></td></tr>
			<tr><td  valign="top" align="left" colspan="3">Current AuthProg: Last1 Init1(CProg1,VProg2)^Last2 Init2(CProg)^: <input type="text" size="60" name = "author_prog" value="<?php echo $author_prog; ?>"></td></tr>
			<tr><td  valign="top" align="left" colspan="3">Prop AuthProg 1: Last1 Init1(P1Prog1,P1Prog2)^Last2 Init2(P1Prog)^: <input type="text" size="60" name = "author_prog_prop_1" value="<?php echo $author_prog_prop_1; ?>"></td></tr>
			<tr><td  valign="top" align="left" colspan="3">Prop AuthProg 2: Last1 Init1(P2Prog1,P2Prog2)^Last2 Init2(P2Prog)^: <input type="text" size="60" name = "author_prog_prop_2" value="<?php echo $author_prog_prop_2; ?>"></td></tr>

<?php
	$autharr = explode(",",$authors);
	$sugcurnames = "";
	$sugpp1names = "";
	$sugpp2names = "";
	$justnames = "";
	// Suggested authors will have issues with center members that have same last name and same first initial.
	// It will also possibly fail with members that have spaces in their last name
	// Be careful for common Last Name and First Initial combinations, there will be a high number of false positives
	//
	// It is really only here as an aid, the user should be able to add any authors that are missed or added incorrectly
	//
	foreach($autharr as $thisauth)
	{
		$thisauth = trim($thisauth);
		$nameparts = explode(" ",$thisauth);
		$numparts = sizeof($nameparts);
		$ind = 0;
		$authqstring = "";
		$authqstring2 = "";
		$delim = "";
		$delim2 = "";
		while ($ind < ($numparts - 1))
		{
			$authqstring .= $delim . $nameparts[$ind];
			$authqstring2 .= $delim2 . $nameparts[$ind];
			
			$delim = " ";
			$delim2 ="-";
			$ind++;
		}
		$authqstring .= " " . substr($nameparts[($numparts-1)],0,1) . "%";
		$authqstring = strtoupper($authqstring);
		$authqstring2 .= " " . substr($nameparts[($numparts-1)],0,1) . "%";
		$authqstring2 = strtoupper($authqstring2);
		
		$authquery = "select * from members where upper(pmsearch) like  $1 or upper (pmsearch) like $2  ";
		$authresult = pg_query_params($mydbh,$authquery,array($authqstring,$authqstring2));
		if (!$authresult) {printf (pg_last_error($mydbh)); exit;}
		$authrow = pg_fetch_array($authresult);
		$dbname = $authrow["pmsearch"];
		if (strlen(trim($dbname)) > 0)
		{
			$dbnameparts = explode(" ",$dbname);
			$dbnumparts = sizeof($dbnameparts);
			$ind = 0;
			$sugname = "";
			while ($ind < ($dbnumparts - 1))
			{
				$sugname .= $dbnameparts[$ind] . " ";
				$ind++;
			}
			$sugname .=  substr($dbnameparts[($dbnumparts-1)],0,1);
			$justnames .= $authrow["name"] . "[" . $sugname . "],";
			if (strlen(trim($authrow["progs"])) > 0)
			{
				$sugcurnames .= $sugname . "(" .  str_replace("|",",", $authrow["progs"]) . ")^";
			}
			if (strlen(trim($authrow["alt_progs_1"])) > 0)
			{
				$sugpp1names .=  $sugname . "(" .  str_replace("|",",", $authrow["alt_progs_1"]) . ")^";
			}
			if (strlen(trim($authrow["alt_progs_2"])) > 0)
			{
				$sugpp2names .=  $sugname . "(" .  str_replace("|",",", $authrow["alt_progs_2"]) . ")^";
			}
		}
	}
?>
	<tr><td align="center" colspan="3"><font face="Arial, Helvetica, sans-serif"><hr /><b>Suggested Center Authors</b><hr /></td></tr>
<?php
	if (strlen(trim($justnames)) > 0)
	{
?>
	<tr><td align="left" colspan="3">It is Suggested, Based on Publication Authorship and Center Membership, that the AuthProg Fields should contain the following:<br><b><?php echo $justnames; ?></b></td></tr>
	<tr><td align="center" colspan="3"><font face="Arial, Helvetica, sans-serif"><input type="button" value="Use Suggested AuthorProg Values" onClick="javascript:usesuggestion();">
	<input type="hidden" name="scnames" value="<?php echo $sugcurnames; ?>">
	<input type="hidden" name="sp1names" value="<?php echo $sugpp1names; ?>">
	<input type="hidden" name="sp2names" value="<?php echo $sugpp2names; ?>">
	</td></tr>
<?php
	}
	else
	{
?>
	<tr><td align="left" colspan="3"><b>Unable to Match Any Center Members to Authirs in This Publication (No SUggestion Available)</b></td></tr>
<?php
	}
?>
	<tr><td align="center" colspan="3"><font face="Arial, Helvetica, sans-serif"><hr /></td></tr>
<?php
if ((strlen(trim($orig_xml)) > 0) || (strlen(trim($updated_xml)) > 0))
{
	echo '<tr><td  colspan="3" align="center">';
	if (strlen(trim($orig_xml)) > 0)
	{
		echo '<input type="button" name=showorigxmlbutton" value="Show Original XML Date From When Publication Was Created" onClick="document.showorigxml.submit();"> &nbsp; &nbsp; ';
	}
	if (strlen(trim($updated_xml)) > 0)
	{
		echo '<input type="button" name=showupdxmlbutton" value="Show XML Data Form Most recent Update" onClick="document.showupdxml.submit();">';
	}
	echo '</td></tr>';
}	
?>
	<tr><td  colspan="3" align="center"><input type="submit" value="Save Changes"><br /><br />
<a href="javascript:returntolist.submit();">Return to Publications Listing</a>

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