<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
$nummonths = 3;
if (isset($_POST['nummonths']))
{
	$nummonths = trim($_POST['nummonths'])*1;
}
if ($nummonths <= 0)
{
	$nummonths = 3;
}
require "ccsgpubs-library.php";
?>
<html>
 <head>
 <title><?php echo $applicationtitle; ?> - Bulk Load New Member publications</title>
 <script>
function docurate(searchnum,thismember)
{
	document.doselect.memberid.value=thismember;
	document.doselect.whichsearch.value=searchnum;
	document.doselect.submit();
}	 
</script>
</head>
<?php 
include "header.php";
?>
 <form name="doselect" method="post" target="_blank" action="selectpubs.php">
 <input type="hidden" name="nummonths" value="<?php echo $nummonths; ?>">
 <input type="hidden" name="memberid">
 <input type="hidden" name="whichsearch">
</form>
<br />
<table width="100%" border="2">
<tr>
<th><i>New</i> Pubs</th>
<th>Details</th>
</tr>
<?php


$query = "select * from members  WHERE MAXDATE > CURRENT_DATE - interval '30 Days' and isactive  order by name";
//$query = "select * from members  WHERE MAXDATE > CURRENT_DATE - interval '30 Days' and isactive and (name like 'SATO%' or name like 'UITTO%' or name like 'DE ROOS%')  order by name";

//$query = "select * from authrange  WHERE MAX > CURRENT_DATE - interval '30 Days' and not skipme AND AUTHNAME LIKE 'A%' order by authname";
//$query = "select * from authrange  WHERE AUTHNAME > 'SIMONE N' AND MAX > CURRENT_DATE - interval '30 Days' and not skipme order by authname";
$result = pg_query_params($mydbh, $query,array());
if (!$result) {printf (pg_ErrorMessage()); exit;}
$stopafter =0;

$geturlstart = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&tool=$toolnameforncbi&email=$emailforncbi&term=";

//while ($row = pg_fetch_array($result) and $stopafter < 10) 
while ($row = pg_fetch_array($result)) 
{
	$stopafter++;
	$authname = $row["name"];
	$memberid = $row["rowid"];
	$pmsearch = $row["pmsearch"];
	$pmsearch2 = $row["pmsearch2"];
	$skipme = $row["skipme"];
	if ($skipme != 't')
	{
		$dispquery = trim($pmsearch) . '[author] AND "last ' . $nummonths . ' months"[dp]';
		$query = str_replace(" ","+",$pmsearch) . '[author]+and+%22last%20' . $nummonths .'%20months%22[dp]';
		
		$retmax = 500;
		$geturl = $geturlstart;
		$geturl .= $query . "&retmax=";
		$geturl .= $retmax;
		$geturl .= "&usehistory=y";
		
	//	echo "geturl:{" . $geturl . "}<br>";
		$uidxml = @simplexml_load_file(urlencode($geturl));
		if (!$uidxml)
		{
			echo "Unable to load UID List . . .";
			echo "<br />geturl $geturl<br />";
			$buffer = file_get_contents(urlencode($geturl));
			echo "<br / >$buffer<br />";
			exit();
		}
		$dosaveuids = 0;
		$alreadysaveduids = 0;
		$alreadyrejecteduids = 0;

		$retcount  = $uidxml->Count;
	//	echo "<br />retcount : $retcount <br />";
		$i = 0;
		while ($i < $retcount )
		{
			$thisuid = $uidxml->IdList->Id[$i];
			//echo "uid[$i]: $thisuid<br />";

			$savethisone  = 1;
			$query2 = 'select count(*) from ccsgpublications where pubmedid = $1  ';
			//echo "<br>query2$query2<br>\n";
			$result2 = pg_query_params($mydbh, $query2,array( $thisuid));
			if (!$result2) {printf (pg_last_error()); exit;}
			$row2 = pg_fetch_array($result2);
	//			var_dump($row2);
			if($row2[0]*1 != 0)
			{
				$savethisone = 0;
				$alreadysaveduids++;
			}
		//			echo "savethisone: $savethisone<br />";
			// now saving memberid with rejections, rejecting it for one member may not mean it should be rejected for all members
			// allowing for null for historical records when I was not storing the member id
			$query3 = 'select count(*) from rejpubs where pubmedid = $1 and (memberid = $2 or memberid is null)' ;
		//			echo "<br>query3$query3<br>\n";
			$result3 = pg_query_params($mydbh, $query3,array( $thisuid,$memberid));
			if (!$result3) {printf (pg_last_error()); exit;}
			$row3 = pg_fetch_array($result3);
		//			var_dump($row3);
			if($row3[0]*1 != 0)
			{
				$savethisone = 0;
				$alreadyrejecteduids++;
			}
		//			echo "savethisone: $savethisone<br />";
			if ($savethisone == 1)
			{
				$dosaveuids++;
			}
					
			$i++;
		}
		$linkstyle = "color: black; ";
		if ($dosaveuids > 0)
		{
			$linkstyle = "color: red; ";
		}
		echo '<tr><td valign="top" nowrap>';
		echo '<a style ="' . $linkstyle .  '" href="javascript:docurate(1,' . $memberid . ');">' . $dosaveuids . ' New Pubs</a>';	
		echo '</td><td valign="top">';
		echo "<b>$authname</b> PubMed Query on {" . $dispquery . "} returned <b>$retcount</b> results.  Already Saved: <b>$alreadysaveduids</b>   Already Rejected:  <b>$alreadyrejecteduids</b>";
		echo '</tr>';
	}
	if (strlen(trim($pmsearch2)) > 0)
	{
		$dispquery = "(" . trim($pmsearch2) . ') AND ("last ' . $nummonths . ' months"[dp])';
		$query = "(" . str_replace(" ","+",$pmsearch2) . ')+and+)%22last%20' . $nummonths .'%20months%22[dp])';
		
		$retmax = 500;
		$geturl = $geturlstart;
		$geturl .= $query . "&retmax=";
		$geturl .= $retmax;
		$geturl .= "&usehistory=y";
		
	//	echo "geturl:{" . $geturl . "}<br>";
		$uidxml = @simplexml_load_file(urlencode($geturl));
		if (!$uidxml)
		{
			echo "Unable to load UID List . . .";
			echo "<br />geturl $geturl<br />";
			$buffer = file_get_contents(urlencode($geturl));
			echo "<br / >$buffer<br />";
			exit();
		}
		$dosaveuids = 0;
		$alreadysaveduids = 0;
		$alreadyrejecteduids = 0;

		$retcount  = $uidxml->Count;
	//	echo "<br />retcount : $retcount <br />";
		$i = 0;
		while ($i < $retcount )
		{
			$thisuid = $uidxml->IdList->Id[$i];
		//	echo "uid[$i]: $thisuid<br />";

			$savethisone  = 1;
			$query2 = 'select count(*) from ccsgpublications where pubmedid = $1' ;
		//	echo "<br>query2$query2<br>\n";
			$result2 = pg_query_params($mydbh, $query2,array( $thisuid));
			if (!$result2) {printf (pg_last_error()); exit;}
			$row2 = pg_fetch_array($result2);
	//			var_dump($row2);
			if($row2[0]*1 != 0)
			{
				$savethisone = 0;
				$alreadysaveduids++;
			}
		//			echo "savethisone: $savethisone<br />";
			// now saving memberid with rejections, rejecting it for one member may not mean it should be rejected for all members
			// allowing for null for historical records when I was not storing the member id
			$query3 = 'select count(*) from rejpubs where pubmedid = $1 and (memberid = $2 or memberid is null)' ;
		//			echo "<br>query3$query3<br>\n";
			$result3 = pg_query_params($mydbh, $query3,array( $thisuid,$memberid));
			if (!$result3) {printf (pg_last_error()); exit;}
			$row3 = pg_fetch_array($result3);
		//			var_dump($row3);
			if($row3[0]*1 != 0)
			{
				$savethisone = 0;
				$alreadyrejecteduids++;
			}
		//			echo "savethisone: $savethisone<br />";
			if ($savethisone == 1)
			{
				$dosaveuids++;
			}
					
			$i++;
		}
		$linkstyle = "color: black; ";
		if ($dosaveuids > 0)
		{
			$linkstyle = "color: red; ";
		}
		echo '<tr><td valign="top" nowrap>';
		echo '<a style ="' . $linkstyle .  '" href="javascript:docurate(2,' . $memberid . ');">' . $dosaveuids . ' New Pubs</a>';	
		echo '</td><td valign="top">';
		echo "<b>$authname</b> PubMed Query on {" . $dispquery . "}  returned <b>$retcount</b> results.  Already Saved: <b>$alreadysaveduids</b>   Already Rejected:  <b>$alreadyrejecteduids</b>";
		echo '</tr>';
	}
	
}
echo '<tr><td colspan="2" align="center"><br /><a href="menu.php">Return to Main Menu</a><br /></td></tr></table><br /><br />';	
include('footer.php');
pg_close($mydbh);
?>