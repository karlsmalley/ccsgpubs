<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
//var_dump($_POST);
$rowsperpage = 10;
$curpage  = 1;
$qrowid = 0;
$qrowfunct = "ge";
$gflabel = " greater than or equal to ";
$gfsymbol = " >= ";
$qstring = "";
$qfield = "author_prog";
$qfdescript = " Current Author-Program Field ";
$colhead = "Curr AuthProg";

$rowidcomp[0] = "";
$rowidcomp[1] = "eq";
$rowidcomp[2] = "ge";
$rowidcomp[3] = "le";
$rowidcomplabel[0] = "";
$rowidcomplabel[1] = " equal to ";
$rowidcomplabel[2] = " greater than or equal to ";
$rowidcomplabel[3] = " less than or equal to ";

$rowidcompsymbol[0] = "";
$rowidcompsymbol[1] = " = ";
$rowidcompsymbol[2] = " >= ";
$rowidcompsymbol[3] = " <= ";
$colwidths = "60|60|65|240|556";

/*
if (isset($_POST["colwidths"]))
{
	$tmpstr = trim($_POST["colwidths"]);
	$tmparr = explode("|",$tmpstr);
	$nonnum = 0;
	$numcol = 0;
	foreach($tmparr as $thisw)
	{
		$thisw = str_replace("px","",$thisw);
		$thisw = str_replace("%","",$thisw);
		if ($thisw*1 == 0)
		{
			$nonnum++;
		}
		$numcol++;
	}	
	if ($numcol == 5 && $nonnum == 0)
	{
		$colwidths = $tmpstr;
	}
	
}
*/

$cw = explode("|",$colwidths);

if (isset($_POST["curpage"]))
{
	$curpage = $_POST["curpage"];
}
if (isset($_POST["qrowid"]))
{
	$qrowid = $_POST["qrowid"] * 1;
}
if (isset($_POST["qrowfunct"]) and strlen(trim($_POST["qrowfunct"])) > 0)
{
	$tmpstr = trim($_POST["qrowfunct"]);
	$k = 0;
	while ($k < 4)
	{
		if ($tmpstr == $rowidcomp[$k])
		{
			$qrowfunct = $rowidcomp[$k];
			$gflabel = $rowidcomplabel[$k];
			$gfsymbol = $rowidcompsymbol[$k];
		}
		$k++;
	}
}


if (isset($_POST["qstring"]) and strlen(trim($_POST["qstring"])) > 0)
{
	$qstring = $_POST["qstring"];
}
if (isset($_POST["qfield"]))
{
	$tmpstr  = trim($_POST["qfield"]);
	if ($tmpstr == 'author_prog')
	{
		$qfield = 'author_prog';
		$qfdescript = " Current Author-Program Field ";
		$colhead = "Curr AuthProg";
	}
	if ($tmpstr == 'author_prog_prop_1')
	{
		$qfdescript = " Proposed Author-Program Field 1 ";
		$qfield = 'author_prog_prop_1';
		$colhead = "Prop AuthProg 1";		
	}
	if ($tmpstr == 'author_prog_prop_2')
	{
		$qfdescript = " Proposed Author-Program Field 2 ";
		$qfield = 'author_prog_prop_2';
		$colhead = "Prop AuthProg 2";		
	}
}

$whereclause = " where 1 = 1 ";
$begrec = ($curpage -1) * $rowsperpage + 1;
$currec = $begrec;
$limitclause = " ";
$doprev = 0;
$qstring = strtoupper ($qstring);
$wheredescript = "";
$andstr ="";
if ($curpage > 1)
{
	$doprev = 1;
	$limitclause = " offset " . ($begrec-1) . " ";
}
$valarray = array();
$numvals = 0; 
if (strlen($qstring) > 0)
{
	$valarray[$numvals] =  "%" . strtoupper(trim($qstring)) . "%";
	$numvals++;
	$whereclause .= " and  $qfield like $" . $numvals;
	$wheredescript .= $andstr . " the $qfdescript contains " .  strtoupper(trim($qstring));
	$andstr = " and ";
}
if ($qrowid > 0)
{

	$valarray[$numvals] =  $qrowid;
	$numvals++;
	$whereclause .= " and  rowid  $gfsymbol  $" . $numvals;
	$wheredescript .=  $andstr .  " the rowid $gflabel $qrowid ";
	$andstr = " and ";	
}	
?>
<html>
<head>
<!-- resizable columns code from http://jsfiddle.net/CU585/  via mikeyriley on stackoverflow  -->
<meta charset="utf-8">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
<style>
th  {border: 1px solid white; font-family: arial; font-size: 9pt; padding: 5px; color: white; }
td.pubtable  {font-family: arial; font-size: 9pt; padding: 5px;}
<!--
table.pubtable {border-collapse: collapse;}
-->
.ui-icon, .ui-widget-content .ui-icon {background-image: none;}
</style>
<script type="text/javascript">
function dodelpub(pubrowid)
{
	document.delpub.pubrowid.value = pubrowid;
	grabcurcolwidths();
	document.delpub.submit();
}
function domodpub(pubrowid)
{
	document.modpub.pubrowid.value = pubrowid;
	grabcurcolwidths();
	document.modpub.submit();
}
function grabcurcolwidths()
{
	return ;
	alert("in grabcurcolwidths");
	var i = 0;
	var delim = "";
	var colwidths = ""
	test = $("th4").width();
	alert("test: " + test )
	while (i < 5)
	{
		var thisw = document.getElementById("th"+i).clientWidth;
		colwidths += delim + thisw;
		delim = "|";
		i++;
	}
	document.delpub.colwidths.value = colwidths;
	document.modpub.colwidths.value = colwidths;
	document.prevpage.colwidths.value = colwidths;
	document.nextpage.colwidths.value = colwidths;
	document.clearsearch.colwidths.value = colwidths;
	document.search.colwidths.value = colwidths;
	
	alert("colwidths: " + colwidths);
}


</script>
<title><?php echo $applicationtitle; ?> -  Center Publication Maintenance Menu</title>
<?php include "header.php"; ?>
<?php
//echo "whereclause: " . $whereclause ."<br>";
//echo "limitclause: " . $limitclause ."<br>";
//echo "wheredescript: " . $wheredescript ."<br>";
$errormsg = "";
if (isset($_POST["action"]))
{
	$action = $_POST["action"];
	if ($action == "DELETE")
	{
		if (isset($_POST["pubrowid"]))
		{
			$thispubrowid = $_POST["pubrowid"]*1;
			$query = 'delete from ccsgpublications where rowid = $1';
			$result = pg_query_params($mydbh, $query, array($thispubrowid));
			if (!$result) {printf (pg_last_error($mydbh)); exit;}
		}
		else
		{
			$erromsg= "Could Not Delete Publication. Publication Row ID Was Missing.";
		}
 	}
}
?>
	<form name="delpub" method="post" action="modpublications.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="qfield" value="<?php echo $qfield; ?>">
    <input type="hidden" name="qrowid" value="<?php echo $qrowid; ?>">
    <input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct; ?>">
    <input type="hidden" name="pubrowid">
    <input type="hidden" name="colwidths">
    <input type="hidden" name="action" value="DELETE">
 	</form>
 	</form>
	<form name="modpub" method="post" action="updpublications.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage); ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>">
    <input type="hidden" name="qfield" value="<?php echo $qfield; ?>">
    <input type="hidden" name="colwidths">
    <input type="hidden" name="qrowid" value="<?php echo $qrowid; ?>">
    <input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct; ?>">
    <input type="hidden" name="pubrowid">
 	</form>
	
	<form name="prevpage" method="post" action="modpublications.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage - 1); ?>">
    <input type="hidden" name="qfield" value="<?php echo $qfield; ?>">
    <input type="hidden" name="qrowid" value="<?php echo $qrowid; ?>">
    <input type="hidden" name="colwidths">
    <input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct; ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>
	<form name="nextpage" method="post" action="modpublications.php">
    <input type="hidden" name="curpage" value="<?php echo ($curpage + 1); ?>">
    <input type="hidden" name="qfield" value="<?php echo $qfield; ?>">
    <input type="hidden" name="colwidths">
    <input type="hidden" name="qrowid" value="<?php echo $qrowid; ?>">
    <input type="hidden" name="qrowfunct" value="<?php echo $qrowfunct; ?>">
    <input type="hidden" name="qstring" value="<?php echo $qstring; ?>"></form>

				
<?php
$query = "SELECT count(*) FROM ccsgpublications " . $whereclause;
//echo "query: " . $query ."<br>";
//echo "<pre>";
//var_dump($valarray);
//echo "</pre><br />";

$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$row = pg_fetch_array($result);
$totcnt = $row[0];

$query = "SELECT * FROM ccsgpublications " . $whereclause . " order by rowid desc" . $limitclause;
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
//echo "query: " . $query ."<br>";

$i = 1;
while(($row = pg_fetch_array ($result)) && ($i <= ($rowsperpage+1)))
{
	//print_r($row);
	$pubrowid[$i] = $row["rowid"];
	$author_prog[$i] = $row["author_prog"];
	$author_prog_prop_1[$i] = $row["author_prog_prop_1"];
	$author_prog_prop_2[$i] = $row["author_prog_prop_2"];
	$dispauth[$i] = $row[$qfield];
	$authorsfull[$i] = $row["authorsfull"];
	$intraconsortium[$i] = $row["intraconsortium"];
	$disptext[$i] = $row["disptext"];
	$authors[$i] =  $row["authors"];
	$pubmedid[$i] =  $row["pubmedid"];
	$pmcid[$i] =  $row["pmcid"];
	$tmpauth = trim($dispauth[$i]);
	$tmptxt = $disptext[$i];
	$tmparr = explode("^",$tmpauth);
	foreach($tmparr as $thisauthstr)
	{
		if (strlen(trim($thisauthstr)) > 0)
		{
			$progstart = strpos($thisauthstr, '(');
			$progend = strpos($thisauthstr,')');
			$thisauth = trim(substr($thisauthstr,0,$progstart));
			$begauth = strpos(strtoupper($tmptxt),$thisauth);
			$endauth = $begauth + strlen($thisauth)-1;
			$keepgoing = 1;
			$curchar = "";
			$thisprog= substr($thisauthstr,$progstart+1,$progend-$progstart-1);
			$seekauth = substr($tmptxt,$begauth,$endauth-$begauth+1);
			while($keepgoing)
			{
				$seekauth .= $curchar;
				$endauth++;
				$curchar = substr($tmptxt,$endauth,1); 
				
				if ($curchar == " ")
				{
					$keepgoing = 0;
				}
				if ($curchar == ".")
				{
					$keepgoing = 0;
				}
				if ($curchar == ",")
				{
					$keepgoing = 0;
				}
			}
			$endauth--;
			$tmptxt = str_replace($seekauth,"<b>" . $seekauth . "[" . $thisprog . "]</b>",$tmptxt);
			$disptext[$i] = $tmptxt;
		}
	}


	$i++;
}

if ($i > ($rowsperpage+1))
{
	$donext = 1;
	$i = $rowsperpage;
}
else
{
	$donext = 0;
	$i--;
}
$numpubs = $i;
$lastrec = $currec + $numpubs -1;					 
?>
<table width="100%"><tr><td align="center">
<table width="100%">
<tr><td colspan = "3" align="center"><font face="Arial, Helvetica, sans-serif" size="4">Publication List</td></tr>
<tr><td align="center" colspan = "3"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" ><b>
Displaying Records <?php echo $currec; ?> - <?php echo $lastrec; ?> of <?php echo $totcnt; ?> records <?php echo $wheredescript; ?>
</b><br><br></td></tr>
<form name="clearsearch" action="modpublications.php" method="POST" onSubmit="grabcurcolwidths();">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="qstring" value="">
<input type="hidden" name="colwidths">
<input type="hidden" name="qfield" value="">
<input type="hidden" name="qrowid" value="">
<input type="hidden" name="qrowfunct" value="">
</form>
<form name="search" action="modpublications.php" method="POST" onSubmit="grabcurcolwidths();">
<input type="hidden" name="curpage" value="1">
<input type="hidden" name="colwidths">
<tr><td colspan="3" nowrap="nowrap" align="center"><font face="Arial, Helvetica, sans-serif" color="#000000" size="2" >
<select name="qfield">
<?php
$optval[0] = "";
$optval[1] = "author_prog";
$optval[2] = "author_prog_prop_1";
$optval[3] = "author_prog_prop_2";
$optlab[0] = "";
$optlab[1] = "Current Author-Program Field";
$optlab[2] = "Proposed Author-Program Field 1";
$optlab[3] = "Proposed Author-Program Field 2";
$k = 0;
while ($k < 4)
{
	$seltxt = "";
	if ($optval[$k] == $qfield)
	{
		$seltxt = " selected ";
	}
	echo '<option ' . $seltxt . ' value="' .  $optval[$k] . '" >' . $optlab[$k] . '</option>';
	echo "\n";
	$k++;
}
?>
</select>  Contains: <input type="text" size="10" name="qstring" value="<?php echo $qstring; ?>"><br /><br />
RowID <select name="qrowfunct">
<?php



$k=0;
while ($k < 4)
{
	$seltxt = "";
	if ($qrowfunct ==  $rowidcomp[$k])
	{
		$seltxt = " selected ";
	}
	echo '<option ' . $seltxt . ' value="' . $rowidcomp[$k] . '" >' . $rowidcomplabel[$k] . '</option>';
	echo "\n";
	$k++;
}
?>
</select> &nbsp;  <input type="text" size="6" name="qrowid" value="<?php echo $qrowid; ?>"></br><br />
<input type="submit" value="Search"><input type="button"  value="Clear Search" onClick="javascript:grabcurcolwidths(); document.clearsearch.submit();">
<br><br></td></tr>
<?php

echo '<tr><td align="left" valign="bottom">';
if ($doprev)
{
	echo '<input type="button" onClick="javascript:grabcurcolwidths();prevpage.submit();" value="<">';
}
else
{
	echo '<input type="button"  disabled value="<">';
}
echo "</td>";

?>
<td  align="center" valign="bottom">
Click on RowID to modify a publication entry.<br />Add publications via the BulkLoad or Manual Add By PubMed ID<br />
Currently publications can only be loaded from PubMed Directly. 
</td>
<?php
echo '<td align="right" valign="bottom">';
if ($donext)
{
	echo '<input type="button" onClick="javascript:grabcurcolwidths(); nextpage.submit();" value=">">';
}
else
{
	echo '<input type="button" disabled value=">">';
}
echo "</td></form></tr>";
?>
</td></tr></table>
<table>
<tr bgcolor="#0000A0">
<th id="th0" style="width: <?php echo $cw[0]; ?>px;">Del</th>
<th id="th1" style="width: <?php echo $cw[1]; ?>px;">RowID</th>
<th id="th2" style="width: <?php echo $cw[2]; ?>px;">PubMed ID</th>
<th id="th3" style="width: <?php echo $cw[3]; ?>px;"><?php echo $colhead; ?></th>
<th id="th4" style="width: <?php echo $cw[4]; ?>px;">Display Text</th>
<!--
<th id="th3"><div class=pubcell" style="color: white; font-face:times; font-size: 10pt;">Prop AuthProg1</div></th>
<th id="th4"><div class=pubcell" style="color: white; font-face:times; font-size: 10pt;">Prop AuthProg2</div></th>
<th id="th5"><div class=pubcell" style="color: white; font-face:times; font-size: 10pt;">Authors Full</div></th>
<th id="th6"><div class=pubcell" style="color: white; font-face:times; font-size: 10pt;">IntraConsortium</div></th>
<th id="th9"><div class=pubcell" style="color: white; font-face:times; font-size: 10pt;">PMC ID</div></th>
-->
</tr>
<?php
$bgcolor = "#F2F2F2";
$i = 1;
while($i <= $numpubs)
{
	if ($bgcolor == "#FFFFFF")
	{
		$bgcolor = "#F2F2F2";
	}
	else
	{
		$bgcolor = "#FFFFFF";
	}

	echo '<tr bgcolor="' . $bgcolor . '">';
    echo '<td class="pubtable" nowrap valign="top" align="center">';
    echo '<input type="button" value="X" onClick="javascript:dodelpub(' . $pubrowid[$i] . ');"></td>';

    echo '<td  class="pubtable" nowrap valign="top" align="center">';
    echo '<a href="javascript:domodpub(' . $pubrowid[$i] . ');"> '  . $pubrowid[$i] . '</td>';

	echo '<td  class="pubtable" nowrap valign="top" align="left">';
    echo $pubmedid[$i];
	echo '</td>';

	echo '<td  class="pubtable" valign="top" align="left">';
//    echo $author_prog[$i];
    echo $dispauth[$i];
	echo '</td>';

	echo '<td  class="pubtable" valign="top align="left">';
    echo $disptext[$i];
	echo '</td>';

/*
	echo '<td nowrap align="left"><div class="pubcell" style="width:' .  $cw[3] .   'px;">';
    echo $author_prog_prop_1[$i];
	echo '</div></td>';

	echo '<td nowrap align="left"><div class="pubcell" style="width:' .  $cw[4] .   'px;">';
    echo $author_prog_prop_2[$i];
	echo '</div></td>';
	

	echo '<td nowrap align="left"><div class="pubcell" style="width:' .  $cw[5] .   'px;">';
    echo $authorsfull[$i];
	echo '</div></td>';
	
	$ictext = 'NO';
	if ($intraconsortium[$i] =='t')
	{
		$ictext = 'YES';
	}

	echo '<td nowrap align="left"><div class="pubcell" style="width:' .  $cw[6] .   'px;">';
    echo $ictext;
	echo '</div></td>';


//	echo '<td nowrap align="left">';
//  echo $authors[$i];
//	echo '</td>';


	echo '<td nowrap align="left"><div class="pubcell" style="width:' .  $cw[9] .   'px;">';
    echo $pmcid[$i];
	echo '</div></td>';
*/
    echo '</tr>';
   	$i++;
 }
?>
<td colspan="6" align="center"><br /><br />
<a href="menu.php">Return to Main Menu</a><br><br>
</td></tr></table></td></tr></table></form>
<script>
$( "th" ).resizable();
</script>
<?php
pg_close($mydbh);
include("footer.php");
?>