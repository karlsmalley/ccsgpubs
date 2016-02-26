<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php require "ccsgpubs-library.php"; ?>
<html>
 <head>
<meta charset="utf-8">
 <title><?php echo $applicationtitle; ?> - Bulk Load New Member publications</title>
<script type="text/javascript">
var rejcolor = "yellow";
var addcolor = "white";
</script>
<script type="text/javascript">
function addall()
{
	//alert("in addall");
	var uidcnt = document.savenewpubs.uidcnt.value;
	//alert("uidcnt: " + uidcnt);
	var i = 0;
	while (i < uidcnt)
	{
		var addbutton  = document.getElementById("add_" + i);
		addbutton.checked = true;
		var rejbutton  = document.getElementById("rej_" + i);	
		rejbutton.checked = false;
		var rowel = document.getElementById("row_" + i);
		rowel.style.backgroundColor = addcolor;
		i++;
	}
}
function rejall()
{
	var uidcnt = document.savenewpubs.uidcnt.value;
	var i = 0;
	while (i < uidcnt)
	{
		var addbutton  = document.getElementById("add_" + i);
		addbutton.checked = false;
		var rejbutton  = document.getElementById("rej_" + i);	
		rejbutton.checked = true;
		var rowel = document.getElementById("row_" + i);
		rowel.style.backgroundColor = rejcolor;
		i++;
	}
}
function invert()
{
	var uidcnt = document.savenewpubs.uidcnt.value;
	var i = 0;
	while (i < uidcnt)
	{
		var addbutton  = document.getElementById("add_" + i);
		var doadd = true;
		if (addbutton.checked)
		{
			doadd = false;
		}
		var rejbutton  = document.getElementById("rej_" + i);
		var dorej = true;
		if (rejbutton.checked)
		{
			dorej = false;
		}
		if (dorej && doadd)
		{
			dorej = false;
		}
		if (!dorej && !doadd)
		{
			doadd = true;
		}
		var rowel = document.getElementById("row_" + i);
		if (doadd)
		{
			rejbutton.checked = false;
			addbutton.checked = true;
			rowel.style.backgroundColor = addcolor;
			
		}
		else
		{
			rejbutton.checked = true;
			addbutton.checked = false;
			rowel.style.backgroundColor = rejcolor;
		}
		i++;
	}
}
</script>
<script type="text/javascript">
function highlight_row(rownum)
{
	var addbutton  = document.getElementById("add_" + rownum);
	var rejbutton  = document.getElementById("rej_" + rownum);
	//alert("addbutton:" + addbutton.checked);
	//alert("rejbutton:" + rejbutton.checked);
	var rowel = document.getElementById("row_" + rownum);
	if (addbutton.checked)
	{
		rowel.style.backgroundColor = addcolor;
	}
	if (rejbutton.checked)
	{
		rowel.style.backgroundColor = rejcolor;
	}
}
</script>
</head>
<?php
include "header.php";

$nummonths = 3;
$memberid = 0;
$whichsearch = 0;

if (isset($_POST["memberid"]))
{
	$memberid = $_POST["memberid"]*1;
}
if (isset($_POST["whichsearch"]))
{
	$whichsearch = $_POST["whichsearch"]*1;
}
if (($whichsearch != 1) && ($whichsearch != 2))
{
	$whichsearch = 1;
}
if (isset($_POST["nummonths"]))
{
	$nummonths = $_POST["nummonths"]*1;
}
if ($nummonths <= 0)
{
	$nummonths = 3;
}
if ($memberid == 0)
{
	echo "<b>Unable To Find A Member ID for this function. Please Use The Application Pages In The Proper Order.<br />";
	exit();
	
}


$membquery = "select * from members where rowid = $1";
$membresult = pg_query_params($mydbh, $membquery,array($memberid));
if (!$membresult) {printf (pg_ErrorMessage()); exit;}
$membrow = pg_fetch_array($membresult);
if(!$membrow)
{
	echo "Search Form Member Record with Member ID: $memberid Failed<br />";
	exit();
}
$authname = $membrow["name"];
$pmsearch = $membrow["pmsearch"];
$pmsearch2 = $membrow["pmsearch2"];
$nameparts = explode(" ", $pmsearch);
$seli = 0;
$hauth = "";
// echo $pmsearch;

while ($seli < (sizeof($nameparts) -1))
{
	$hauth .= $nameparts[$seli] . " ";
	$seli++;
}
$hauth .= substr($nameparts[$seli],0,1);
$geturl = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&tool=$toolnameforncbi&email=$emailforncbi&term=";

if ($whichsearch ==  1)
{
	$dispquery = $pmsearch . '[author] AND "last ' . $nummonths . ' months "[dp]';
	$query = str_replace(" ","+",$pmsearch) .'[AUTHOR]+and+%22last%20' . $nummonths . '%20months%22[dp]';
}
else
{
	$dispquery = '(' . $pmsearch2 . ') AND "last ' . $nummonths . ' months "[dp]';
	$query = '(' .str_replace(" ","+",$pmsearch2) .')+and+%22last%20' . $nummonths . '%20months%22[dp]';
	
}
		
$retmax = 500;

$geturl .=  $query . "&retmax=";
$geturl .= $retmax;
$geturl .= "&usehistory=y";
	

$uidxml = @simplexml_load_file(urlencode($geturl));
if (!$uidxml)
{
	echo "Unable to load UID List . . .";
	echo "<br />geturl $geturl<br />";
	$buffer = file_get_contents(urlencode($geturl));
	echo "<br / >$buffer<br />";
	exit();
}
$uidcnt= 0;
$rejuidcnt = 0;
$alreadyloaded = 0;
//echo "geturl: $geturl<br /><br />";
$retcount  = $uidxml->Count;
echo "<br>PubMed Query on {" . $dispquery . "} returned " .$retcount ." results.<br>";
ob_flush();
flush();
//	echo "<br />retcount : $retcount <br />";
$seli = 0;

while ($seli < $retcount )
{
	$thisuid = $uidxml->IdList->Id[$seli];
// echo '<a href="' . $testquery . '" target="_blank">Check Pubs</a><br><br>';	

	$query2 = 'select count(*) from ccsgpublications where pubmedid = $1' ;
//		echo "<br>query2$query2<br>\n";
	$result2 = pg_query_params($mydbh, $query2,array($thisuid));
	if (!$result2) {printf (pg_last_error($db)); exit;}
	$row = pg_fetch_array($result2);
	if($row[0] == 0)
	{
			// now saving memberid with rejections, rejecting it for one member may not mean it should be rejected for all members
			// allowing for null for historical records when I was not storing the member id
			$query3 = 'select count(*) from rejpubs where pubmedid = $1 and (memberid = $2 or memberid is null)' ;
		//			echo "<br>query3$query3<br>\n";
			$result3 = pg_query_params($mydbh, $query3,array( $thisuid,$memberid));
		if (!$result3) {printf (pg_last_error($db)); exit;}
		$row3 = pg_fetch_array($result3);
		if($row3[0] > 0)
		{
			$rejuid[$rejuidcnt] = $thisuid;
			$rejuidcnt++;
		}
		else
		{
			$uidarr[$uidcnt] = $thisuid;
//		print "{" . $uidarr[$uidcnt] . "}<br>";
			$uidcnt++;
		}
	}
	else
	{
		$alreadyloaded++;
	}
	$seli++;
}
echo "$alreadyloaded Publications are already in the publication tables. $uidcnt Publications will be loaded for displayed below for evaluation. $rejuidcnt Publications have previously been rejected.<br />";
ob_flush();
flush();
$seli = 0;
echo '<form name="savenewpubs" method="POST" action="savenewpubs.php">';
echo '<input name="memberid" id="memberid" type="hidden" value="' . $memberid . '">';

echo '<input name=uidcnt" id="uidcnt" type="hidden" value="' . $uidcnt . '">';
echo '<table border="1">';
$tmparr = explode(" ",$pmsearch);
//print_r($tmparr);
$mylastname = trim(ucwords(strtolower($tmparr[0])));
while($seli <  $uidcnt)
{
	$pubid = $uidarr[$seli];
ob_flush();
flush();

//	echo "pubid: $pubid<br />";
	include "parse-pub.php";

	$dispfullauth = str_ireplace($mylastname,'<font color="red">' . $mylastname . '</font>',$authandaffil);
	$dispfullauth = str_ireplace('Drexel','<font color="red">Drexel</font>',$dispfullauth);
	$dispfullauth = str_ireplace('Lankenau','<font color="red">Lankenau</font>',$dispfullauth);
	$dispfullauth = str_ireplace('Jefferson','<font color="red">Jefferson</font>',$dispfullauth);
	
	//echo "$fullauth<br  />";
	echo '<input type="hidden" name="uid_' . $seli . '" value="' . $pubid . '">' . "\n";
	echo '<tr id="row_' . $seli . '"><td valign="top" style="white-space:nowrap"><input type="radio" id="add_' . $seli . '" name="save_' . $seli . '" checked value="save" onClick="highlight_row(' . $seli . ');" > Add <br>';
	echo '<input type="radio" id="rej_' . $seli . '" name="save_' . $seli . '" value="reject" onClick="highlight_row(' . $seli . ');" > Reject';
	echo '</td><td>';
	echo str_replace(ucwords(strtolower($hauth)),"<b>" . ucwords(strtolower($hauth)) . "</b>",$disptext);
	echo "<br>" . $dispfullauth . '<br><a href="' . $url2 . '" target="_blank">Display Pubmed Record</a></td></tr>';
	ob_flush();
	flush();

	$seli++;
}
echo '</table><br><br><input type="submit" value="Process above Publications">';
echo '&nbsp; &nbsp;<input type="button" value="Add All" onClick="javascript:addall();">';
echo '&nbsp; &nbsp;<input type="button" value="Reject All" onClick="javascript:rejall();">';
echo '&nbsp; &nbsp;<input type="button" value="Invert Selection" onClick="javascript:invert();"><br />';



echo '</form><br /><br />';
echo "Previously Rejected Publications($rejuidcnt)<br />";
echo '<table border="1">';
$seli = 0;
while($seli <  $rejuidcnt)
{
	$pubid = $rejuid[$seli];
	$seli++;
	ob_flush();
	flush();
	include "parse-pub.php";
	echo '<tr>';
	echo "<td>$pubid  ($seli of $rejuidcnt)</td><td>";
	$dispfullauth = str_replace($mylastname,'<font color="red">' . $mylastname . '</font>',$fullauth);
	//echo "$fullauth<br  />";
	echo str_replace(ucwords(strtolower($authname)),"<b>" . ucwords(strtolower($authname)) . "</b>",$disptext);
	echo "<br>" . $dispfullauth . '<br><a href="' . $url2 . '" target="_blank">Display Pubmed Record</a></td></tr>';
	ob_flush();
	flush();
}
echo '</table><br><br>';
pg_close($mydbh);
include('footer.php');
?>

