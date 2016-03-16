<?php
// This script should be available to anyone who might need to see the publications  and not necessarily someone who might need to modify/update publications.
// For this reason it is launched as a new Window/Tab for the rest of the application.. Has no links back to the application. 
// Nor does it use session variables created by the application.
// However we would still like tio include the common library file.

require "ccsgpubs-library.php";
$cutoff = 10;
$cutoff = 9;
$lowyear = 2000;
$query = "select max(date_part('year',pubdate)) from ccsgpublications";
$result = pg_exec($mydbh, $query);
if (!$result) {printf (pg_ErrorMessage()); exit;}
$maxrow = pg_fetch_array($result);
$hiyear = $maxrow[0]*1;
?>
<title>SKCC Publications By Year and Journal</title>
</head>
<body>
<h1>SKCC Publications By Year and Journal</h1><br />
<table border="1">
<?php
//echo "lowyear: $lowyear<br>";
//echo "hiyear: $hiyear<br>";
//$i = $lowyear;
//while ($i <= $hiyear)
//{
//	$totyr[$i] = 0;
//	echo '<td>' . $i . '</td>';
//	$i++;
//}
//echo '<td>TOTAL</td>';
//echo '</tr>';
//echo "\n";

$curyear = $hiyear+1;
$lagjournal = 'ZZZ'; 
$numcols = $hiyear - $lowyear + 3;
$grandtot = 0;
$didheader=0;
$i = $lowyear;
while ($i <= $hiyear)
{
	$subtot[$i] = 0;
	$totyr[$i] = 0;
	$i++;
}





$query = "select imp_factors.impact_factor_13_14, ccsgpublications.journal, ccsgpublications.journal_issn, ";
$query .= "date_part('year',ccsgpublications.pubdate) as pubyear,count(*) as pubcount ";
$query .= "from ccsgpublications, imp_fac_link ";
$query .= "left join imp_factors on (imp_fac_link.impact_factor_issn=imp_factors.issn) ";
$query .= "where ccsgpublications.journal_issn = imp_fac_link.publication_issn ";
$query .= "group by imp_factors.impact_factor_13_14,ccsgpublications.journal,ccsgpublications.journal_issn,date_part('year',ccsgpublications.pubdate) ";
$query .= "order by imp_factors.impact_factor_13_14 desc NULLS LAST, ccsgpublications.journal ";
//$query = "select imp_factors.impact_factor_13_14, ccsgpublications.journal, ccsgpublications.journal_issn, date_part('year',ccsgpublications.pubdate) as pubyear,count(*) as pubcount ";
//$query .= " from ccsgpublications left join imp_factors on (ccsgpublications.journal_issn=imp_factors.issn) ";
//$query .= " group by imp_factors.impact_factor_13_14,ccsgpublications.journal,ccsgpublications.journal_issn,date_part('year',ccsgpublications.pubdate)  ";
//$query .= " order by imp_factors.impact_factor_13_14 desc NULLS LAST,  ccsgpublications.journal ";

//echo "query: $query<br />";

$result = pg_exec($mydbh, $query);
if (!$result) {printf (pg_ErrorMessage()); exit;}
while ($sumrow = pg_fetch_array($result))
{
	$thisjournal = $sumrow["journal"];
	$issn = $sumrow["journal_issn"];
	if ($thisjournal != $lagjournal)
	{
		if ($lagjournal != 'ZZZ')
		{
			while ($curyear <= $hiyear)
			{
				echo '<td>&nbsp;</td>';
				$curyear++;
			}
			echo '<td align="right">' . $jcount . '</td>';
			echo '</tr>';
		}
			
		$jcount = 0;
		$doyears = 0;
		$curyear = $lowyear;
		if ($didheader == 0 && $sumrow["impact_factor_13_14"]*1 > $cutoff)
		{
			echo '<tr><td bgcolor="#23659D" align="center" colspan="' . $numcols . '"><font color="white">High Impact Journals</font></td></tr>';
			$didheader++;
			$doyears = 1;
		}
		if ($didheader == 1 && $sumrow["impact_factor_13_14"] < $cutoff && $sumrow["impact_factor_13_14"] > 0)
		{
			$subgtot = 0;
			echo '<tr bgcolor="lightgray"><td>SUB-TOTAL</td>';
			$i = $lowyear;
			while ($i <= $hiyear)
			{
				echo '<td align="right" >' . $subtot[$i] . '</td>';
				$subgtot += $subtot[$i];
				$i++;
			}
			echo '<td align="right">' . $subgtot . '</td>';
			echo '</tr>';
			echo "\n";
			echo '<tr><td bgcolor="#23659D" align="center"  colspan="' . $numcols . '"><font color="white">Other Journals For Which We Know The Impact Factor</font></td></tr>';
			$didheader++;
			$doyears = 1;
		}
		if ($didheader == 2 && $sumrow["impact_factor_13_14"] <= 0)
		{
			$subgtot = 0;
			echo '<tr bgcolor="lightgray"><td>SUB-TOTAL</td>';
			$i = $lowyear;
			while ($i <= $hiyear)
			{
				echo '<td align="right" >' . $subtot[$i] . '</td>';
				$subgtot += $subtot[$i];
				$i++;
			}
			echo '<td align="right">' . $subgtot . '</td>';
			echo '</tr>';
			echo "\n";
			echo '<tr><td bgcolor="#23659D" align="center"  colspan="' . $numcols . '"><font color="white">Journals Where The Impact Factor is Undefined or Unknown</font></td></tr>';
			$didheader++;
			$doyears = 1;
		}
		if ($doyears)
		{
			echo '<tr bgcolor="#23659D"><td><font color="white">Journal [2013/14 IF]{<i>ISSN</i>}</font></td>';
			$i = $lowyear;
			while ($i <= $hiyear)
			{
				echo '<td><font color="white">' . $i . '<font color="white"></td>';
				$subtot[$i] = 0;
				$i++;
			}
			echo '<td><font color="white">TOTAL<font color="white"></td>';
			echo '</tr>';
			echo "\n";
		}
		$impfact = $sumrow["impact_factor_13_14"]*1;
		if ($impfact == 0)
		{
			$impfact = "Unknown";
		}
		if ($impfact == -99)
		{
			$impfact = "Undefined";
		}
		
		echo '<tr><td>' . $thisjournal . ' [' . $impfact . ']{<i>ISSN:' . $issn . '</i>}</td>';
		$lagjournal = $thisjournal;
	}
	$thisyear = $sumrow["pubyear"];
	$thiscount = $sumrow["pubcount"];
	while ($curyear < $thisyear)
	{
		echo '<td>&nbsp;</td>';
		$curyear++;
	}
	echo '<td align="right">' . $thiscount . '</td>';
	$totyr[$curyear] += $thiscount;
	if (!isset($subtot[$curyear]))
	{
		$subtot[$curyear] = 0;
	}
	$subtot[$curyear] += $thiscount;
	$jcount += $thiscount;
	$grandtot += $thiscount;
	$curyear++;
}
while ($curyear <= $hiyear)
{
	echo '<td>&nbsp;</td>';
	$curyear++;
}
echo '<td align="right" >' . $jcount . '</td>';
echo '</tr>';
echo "\n";

$subgtot = 0;
echo '<tr bgcolor="lightgray"><td>SUB-TOTAL</td>';
$i = $lowyear;
while ($i <= $hiyear)
{
	echo '<td align="right" >' . $subtot[$i] . '</td>';
	$subgtot += $subtot[$i];
	$i++;
}
echo '<td align="right">' . $subgtot . '</td>';
echo '</tr>';
echo "\n";


echo '<tr bgcolor="lightgray"><td>TOTAL</td>';
$i = $lowyear;
while ($i <= $hiyear)
{
	echo '<td align="right" >' . $totyr[$i] . '</td>';
	$i++;
}
echo '<td align="right">' . $grandtot . '</td>';
echo '</tr></table>';
echo "\n";
$query = "select * from systemconf where rowid = 1" ;
$result = pg_query_params($mydbh, $query,array());
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$lmrow = pg_fetch_array($result);
//rint_r($lmrow);
$tmpstr = $lmrow["lastmod"];
//echo "<br />tmpstr: $tmpstr<br />";
$tmparr = explode("-",$tmpstr);
//print_r($tmparr);
$lastmod = $tmparr[1] . "/" . $tmparr[2] . "/" . $tmparr[0];
echo '<br /><br />';
echo "<i>The SKCC Publication database is usually updated weekly or bi-weekly, the last update was completed on $lastmod.</i><br />";
echo '<i>Currently suing 2013/14 Journal Impact Factors from <a href="http://www.citefactor.org/journal-impact-factor-list-2014.html" target="_blank">citefactor.org</a></i>';
echo '<br /><br />Year of publication may be either the EPub Date or Print -- but each publication is only counted once<bt /><br />';
echo 'Return To The <a href="pubmenu.php">Publication Report Menu</a><br/><br />';
echo '</body></html>';
?>

