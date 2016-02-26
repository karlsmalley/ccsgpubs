<?php header('Content-type: text/html; charset=utf-8'); ?>
<html>
<head>
<style>
td {font-family: Arial; size: 12pt;}
p {margin-left: 20px; font-family: Arial; size: 12pt;}
</style>
<?php
require "ccsgpubs-library.php";
$useprop1= 0;
$useprop2 = 0;

if(isset($_GET["useprop1"]))
{
	$useprop1 = $_GET["useprop1"]*1;
}
if(isset($_GET["useprop2"]))
{
	$useprop2 = $_GET["useprop2"]*1;
}
// Even get Inactive Programs For Historical Publications (Program  Closed Or Renamed -- Codes Changed {Numeric to Alphabetic})
$progquery = "select * from programs order by sortnum";
$progresult = pg_query_params($mydbh,$progquery,array());
if (!$progresult) {printf (pg_last_error($mydbh)); exit;}
$numprogs = 0;
$thisproglist = "";
$othproglist = "";
$oldproglist = "";
// Load all programs ever used
while ($progrow = pg_fetch_array($progresult))
{
	$thiscode = $progrow['program_code'];
	$prognamelist[$thiscode] = $progrow['program_name'];
	$progcodes[$numprogs] = $thiscode;
	// All Programs shoulf dit in one of these categories
	// 		Programs can be listed as Current and in the Selected Program Structure
	//		Alternate Programs (Currently Active but not included in the Selected Program Structure (Proposed or Developing 
	//    			Program in one of the Alternate Proposed Program Structures))
	// 		Older Program -- INATIVE Older Program Name for a Current Program Or different coding scheme, 
	//				numeric versus alphabetic or just a closed program,
	if ($progrow["isactive"] == 't')
	{
		$added = 0;
		if ($progrow["iscurrprog"] == 't' && !$useprop1 && !$useprop2)
		{
			$thisproglist .= $progrow['program_code'] . ":   " . $progrow['program_name'] . "<br />";
			$added = 1;
		}
		if ($progrow["isaltprog1"] == 't' && $useprop1 )
		{
			$thisproglist .= $progrow['program_code'] . ":   " . $progrow['program_name'] . "<br />";
			$added = 1;
		}
		if ($progrow["isaltprog2"] == 't' && $useprop2 )
		{
			$thisproglist .= $progrow['program_code'] . ":   " . $progrow['program_name'] . "<br />";
			$added = 1;
		}
		if (!$added)
		{
			$othproglist .= $progrow['program_code'] . ":   " . $progrow['program_name'] . "<br />";
		}
	}
	else
	{
		$oldproglist .= $progrow['program_code'] . ":   " . $progrow['program_name'] . "<br />";
	}
	$numprogs++;
}

// Publciaiton Names used throughout history of SKCC 
/*
$prognamelist[0]= "Non-Programmatically Aligned";
$prognamelist[1]= "Cellular Biology & Signaling";
$prognamelist[2]= "Genetics & Molecular Biology";
$prognamelist[3]= "Structural Biology";
$prognamelist[4]= "Immunology";
$prognamelist[5]= "Developmental Therapeutics";
$prognamelist[6]= "Hematologic Malignancies and Stem Cell Transplantation";
$prognamelist[7]= "Gastrointestinal Cancer";
$prognamelist['CCBS']= "Cancer Cell Biology & Signaling";
$prognamelist['MBG']= "Molecular Biology & Genetics";
$prognamelist['IMC']= "Immunological Mechanisms in Cancer";
$prognamelist['EMHAC']= "Endocrine Mechanisms and Hormone Action in Cancer";
$prognamelist['RRTB']= "Radiation Research & Translational Biology";
$prognamelist['GIC']= "Gastrointestinal Cancer";
$prognamelist['ZY']= "Non-Programmatically Aligned";
$prognamelist['BPC']= "Biology of Prostate Cancer";
#$prognamelist['BWC']= "Biology of Women's Cancer";
$prognamelist['BBC']= "Biology of Breast Cancer";
$prognamelist['PS']= "Population Science (Developing)";
$prognamelist['CRC']= "Cancer Risk & Control (Developing)";


$progcodes[0] = '0';
$progcodes[1] = '1';
$progcodes[2] = '2';
$progcodes[3] = '3';
$progcodes[4] = '4';
$progcodes[5] = '5';
$progcodes[6] = '6';
$progcodes[7] = '7';
$progcodes[8] = 'CCBS';
$progcodes[9] = 'MBG';
$progcodes[10] = 'IMC';
$progcodes[11] = 'EMHAC';
$progcodes[12] = 'RRTB';
$progcodes[13] = 'GIC';
$progcodes[14] = 'ZY';
$progcodes[15] = 'BPC';
#$progcodes[16] = 'BWC';
$progcodes[16] = 'BBC';
$progcodes[17] = 'PS';
$progcodes[18] = 'CRC';
*/

// Load ONRENDER author filters.
// Keep Data Stored the way it is but modif the display of the data after marking authors (eg Zhnag JI -> Zhang J for Jianke  Zhang's publication with other authors with last name of Zhang and first initial of J)
$filtquery = "select * from filters where filter_when = 'ONRENDER' ";
$filresult = pg_query_params($mydbh,$filtquery,array());
if (!$filresult) {printf (pg_last_error($mydbh)); exit;}

$numfilts = 0;
while($fitlrow = pg_fetch_array($filresult) )
{
	$filtsrch[$numfilts]  = $fitlrow["searchtext"];
	$filtrepl[$numfilts]  = $fitlrow["replacetext"];
	$numfilts++;
}


// parameters for publication Display
// pubmenu uses get, so that a link can easily be sent to others.
//
$isfulllist = 1;	// Full list (not a Program List or Author List)
$isproglist = 0;	// Program Listing -- Only Listing With Italicized Highlighting (also Intra and Inter markings may differ for general listing)
$isauthlist = 0;	// Author Listing 
$skipdate = 0;		// Include 3rd Column with Pubdate and Rowid
$showrowid = 0; 	// Display RowID only valid if skipdate = 0
$showpmcid = 0;		// Displays PubMedID and PubMedCentral ID
$enumerate = 1;		// Displays sequential Number with markings (should be used if not sorting by Author)
$abstract = 1;		// Displays Link To PubmEd Abstract (useful when puttying Progress Reports.renewals together, should not be included in formal listing)
$afterdate = '6/1/2000'; 
$foundafterdate = 0;
$foundstopdate = 0;
$orderby = "pubdate, authors";
$altsort = 0;
$showquery = 0;		// must be added manually to url displays query, begindate, pubcount and program authorship summary
$showdoi = 0;		// Adds doi dat to display
$showpii = 0;		// Adds PII daat to display (this will automatically be added if page numbers are missing)
$showif = 0;		// Shows Impact Factor Next To Journal, also calculates statistics based on Impact Factors
$showinter = 0;		// Marks Inter-consortium publications with Select Symbol (double-S)
$justinter = 0;		// Only show Inter-consortium publications
$selectedjournals = 0;	// Currently not using (was used for filtering on High Impact Journals, etc.)
$higlightfirstlast=0;	// Highlight publicationns if Senior Author or First Author are center members.
$numfirstauth = 0;
$numlastauth = 0;
$numfirstandlast = 0;
$justhigh=0;
if (isset($_GET["showquery"]))
{
	$showquery = 1;
}
if (isset($_GET["higlightfirstlast"]))
{
	$higlightfirstlast = $_GET["higlightfirstlast"];
}
if (isset($_GET["justhigh"]))
{
	$justhigh = $_GET["justhigh"];
}

if (isset($_GET["altsort"]))
{
	$altsort = $_GET["altsort"];
}
if ($altsort == 1)
{
	$orderby = "replace(authors,' ','0'), pubdate";
}
if ($altsort == 2)
{
	$orderby = "rowid";
}
if ($altsort == 3)
{
	$orderby = "journal, pubdate";
}
if ($altsort == 4)
{
	$orderby = "case when impact_factor_13_14  is null then 1 else 0 end, impact_factor_13_14 desc, pubdate";
}
if (isset($_GET["showif"]))
{
	$showif = $_GET["showif"];
}

if (isset($_GET["showinter"]))
{
	$showinter = $_GET["showinter"];
}
if (isset($_GET["justinter"]))
{
	$justinter = $_GET["justinter"];
}


if (isset($_GET["showdoi"]))
{
	$showdoi = $_GET["showdoi"];
}

if (isset($_GET["showpii"]))
{
	$showpii = $_GET["showpii"];
}
if (isset($_GET["showpmcid"]))
{
	$showpmcid = $_GET["showpmcid"];
}
if (isset($_GET["showabstract"]))
{
	$abstract = $_GET["showabstract"];
}
if (isset($_GET["afterdate"]))
{
	$afterdate = $_GET["afterdate"];
	if ($afterdate == "RECENT")
	{
		$afterdate = date('Y-m-d', strtotime("-6 months"));
	}
	if ($afterdate != "")
	{
		$foundafterdate = 1;
	}
	else
	{
		$afterdate = '6/1/2000';
	}

}
$curval = 0;
$valarray[$curval] = $afterdate;
$curval++;
$wclause = ' where pubdate >=  $' . $curval;
$stopdate = "";
if (isset($_GET["stopdate"]))
{
	if ($_GET["stopdate"] != "")
	{
		$stopdate = $_GET["stopdate"];
		$foundstopdate = 1;
		$valarray[$curval] = $stopdate;
		$curval++;
		$wclause .= ' and  pubdate <= $'  . $curval;
	}
}
if (isset($_GET["skipdate"]))
{
	$skipdate = $_GET["skipdate"];
}
if (isset($_GET["enumerate"]))
{
	$enumerate = $_GET["enumerate"];
}
if (isset($_GET["showrowid"]))
{
	$showrowid = $_GET["showrowid"];
}

$header = $centername . " Comprehensive Bibliography";
$stoplabel = "Present";
if ($stopdate != '')
{
	$foundstopdate = 1;
	$stoplabel = $stopdate;
}
if ($afterdate != '')
{
	$header .= ' (' . $afterdate . " - " . $stoplabel . ")";
}
$authfield = "author_prog";
$pubdb = " ccsgpublications";
if ($useprop1)
{
	$authfield = "author_prog_prop_1";
}
if ($useprop2)
{
	$authfield = "author_prog_prop_2";
}

$wclause .= " and ($authfield is not null and $authfield != '') ";


// Many journals haver multiple ISSN numbers and we do not always get the  one CiteFactor uses.
// So I have created a table (imp_fac_link) which links  Multiple Journal ISSN records to the CiteFactor ISSN
$iftable = "imp_factors"; 
$iffield = "impact_factor_12";
$iffield = "impact_factor_13_14";
$joinclause = " ($iftable.issn = imp_fac_link.impact_factor_issn) ";
$jfield = "journal_name";

if ($altsort == 4)
{
//	$orderby = "case when $iffield  is null then 1 else 0 end, $iffield desc, pubdate";
	$orderby = "$iffield desc NULLS LAST , pubdate";
}

if ($altsort == 5)
{
	$orderby = "case when $iffield  is null then 1 else 0 end, $iffield desc, authors";
	$orderby = "$iffield desc NULLS LAST , authors";
}



$prog = "";
if(isset($_GET["prog"]))
{
	$prog = trim($_GET["prog"]);
	$header = $centername . " Bibliography - Program $prog: " . $prognamelist[$prog]; 
	$valarray[$curval] = "%(%" . $prog . "%)%";
	$curval++;
	$wclause .= " and  " . $authfield . " like $" . $curval;
	$isfulllist = 0;
	$isproglist = 1;
	$isauthlist = 0;
}

if(isset($_GET["author"]))
{
	$auth = strtoupper($_GET["author"]);
	$valarray[$curval] = "%" . $auth . "%";
	$curval++;
	$wclause .= " and  " . $authfield . " like $" . $curval;

	$header = $centername . " Bibliography - Author: " . $auth; 
	
	$isfulllist = 0;
	$isproglist = 0;
	$isauthlist = 1;
}

if ($justinter)
{
	$header .= " [Just Inter-Institutional Publications] ";
	$wclause .= " and  itraconsortium ";
	
}
if ($justhigh)
{
	$header .= " [Just Publications With JIF >= 9] ";
	$wclause .= " and  $iftable.$iffield >= 9";
	
}

?>
<title><?php echo $header ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
//include "header.php";
$wclause .= " and  $pubdb.journal_issn  = imp_fac_link.publication_issn ";
//$wclause .= " and  $pubdb.rowid=2544";

$query = "select $iftable.$iffield as jif, $iftable.$jfield as journalname, $pubdb.* from $pubdb, imp_fac_link ";
$query .= " left join $iftable on $joinclause  $wclause  order by $orderby ";

if ($showquery)
{
	echo "query: $query<br><pre>";
	var_dump($valarray);
	echo "</pre><br />";
}
$result = pg_query_params($mydbh, $query,$valarray);
if (!$result) {printf (pg_last_error($mydbh)); exit;}
if ($showquery)
{
	echo "num rows: " . (pg_num_rows($result)) . "<br>";
}
echo '<center><h2>' . $header;
echo '</h2></center>';
echo '<table><tr><td colspan="2" align="center"><b>PUBLICATION</b></td>';
if (!$skipdate)
{
	echo '<td><b>PUB DATE</b></td>';
}
echo '</tr>';
$i = 1;
$numinter = 0;
$numintra = 0;
$numboth = 0;
$numifgt10 = 0;
$numifgt9 = 0;
$sumif = 0;
$denom1 = 0;
$denom2 = 0;
$numinterinst = 0;
//while (($pubrow = pg_fetch_array($result)) && ($i < 255))
while (($pubrow = pg_fetch_array($result)))
{

	$thispubnumprogs = 0;
	$thispubnumauths  = 0;
	$selprog = $prog;
	$pnum = 0;
	$dodagger = 0;   // Impact Factor > 10
	$dodagger2 = 0;  // Impact Factor > 9 but less than 10
	$dosect = 0;	// Section Sign --- (double S) Intraconortium publications
	if ($pubrow["intraconsortium"] == 't')
	{
		$dosect = 1;
		$numinterinst++;
	}
	if ($pubrow["jif"]*1 > 9)
	{
		$numifgt9++;
		$dodagger2 = 1;
	}
	if ($pubrow["jif"]*1 > 10)
	{
		$numifgt10++;
		$dodagger = 1;
		$dodagger2 = 0;
	}
	$impact = $pubrow["jif"]*1;
	$denom2++;
	if ($impact <= 0)
	{
		$impact = "UK";
	}
	else
	{
		$denom1++;
		$sumif += $impact;
	}
	$ifjournal = trim($pubrow["journalname"]);
	$rowid = $pubrow["rowid"];	
	$pubdate = $pubrow["pubdate"];
	$pubmedid = $pubrow["pubmedid"];
	$tmparr = explode("-",$pubdate);
	$pubdate = $tmparr[1] . "/" . $tmparr[2] . "/" . $tmparr[0];
	$disptext = $pubrow["disptext"];
	$kccauthors = $pubrow[$authfield];
	$journal  = $pubrow["journal"];
	$authlist = explode("^",$kccauthors);
	$pmcid = trim($pubrow["pmcid"]);
	$doi = trim($pubrow["doi"]);
	$pii = trim($pubrow["pii"]);
	$pubstatus = trim($pubrow["pubstatus"]);
	$pubauthors = trim($pubrow["authors"]);
	$pages = trim($pubrow["pages"]);
	if ($pages == "" && $pii != "")
	{
		$disptext = trim($disptext,'.');
		$disptext .= " " . $pii;
	}
	$pubauthlist = explode(',' ,$pubauthors);
//	echo '<pre>';
//	var_dump($pubauthlist);
//	echo '</pre>';
	$numpubauth = 0;
	$srauth1 = "";
	$srauth2 = "";
	$firstauth1 = "";
	$firstauth2 = "";
	// Determine the first author and senior author of the current publication
	foreach($pubauthlist as $thispubauth)
	{
		$thispubauth  = trim($thispubauth);
		$thispubauth = str_replace('ó','o',$thispubauth);
		$thispubauth = str_replace('é','e',$thispubauth);
		if(strlen(trim($thispubauth)) > 0)
		{
			$tmpnum = strpos($thispubauth,' ');
			$palast = strtoupper(trim(substr($thispubauth,0,$tmpnum)));
			$pafirst = strtoupper(trim(substr($thispubauth,$tmpnum)));
			$srauth1 = $palast . " " . $pafirst;
			$srauth2 = $palast . " " . substr($pafirst,0,1);
			if ($numpubauth == 0)
			{
				$firstauth1 = $palast . " " . $pafirst;
				$firstauth2 = $palast . " " . substr($pafirst,0,1);
			}
			$numpubauth++;
		}
	}
	
	$inprogcnt = 0;
	$outprogcnt = 0;
	$chcklist = "";
	$j = 0;
	while ($j < $numprogs)
	{
		$prgcnt[$j] = 0;
		$j++;
	}
	$skccfirstauth = 0;
	$skccseniorauth = 0;
 	foreach ($authlist as $thisauthstr)
	{

		if (strlen(trim($thisauthstr)) > 0)
		{
			// First Check TO See If This author Is The First or Senior Auuthor
			$justauth = trim(substr($thisauthstr,0,stripos($thisauthstr,"(")));
			if ($justauth == $firstauth1)
			{
				$skccfirstauth = 1;
			}
			if ($justauth == $firstauth2)
			{
				$skccfirstauth = 1;
			}
			if ($justauth == $srauth1)
			{
				$skccseniorauth = 1;
			}
			if ($justauth == $srauth2)
			{
				$skccseniorauth = 1;
			}
			$thispubnumauths++;

			$progstart = strpos($thisauthstr, '(');
			$progend = strpos($thisauthstr,')');
			$thisauth = trim(substr($thisauthstr,0,$progstart));
			$thisprog= substr($thisauthstr,$progstart+1,$progend-$progstart-1);
			if (!isset($progcheck[$thisprog]))
			{
				$progcheck[$thisprog] = 0;
			}
			$progcheck[$thisprog]++;
			$begauth = strpos(strtoupper($disptext),$thisauth);
			$endauth = $begauth + strlen($thisauth)-1;
			$keepgoing = 1;
			$curchar = "";
			// Find This Author's Position In Disptext, will use this to ad programs and embolden and italicize author (allow for more initials in publication then)
			$seekauth = substr($disptext,$begauth,$endauth-$begauth+1);
			while($keepgoing)
			{
				$seekauth .= $curchar;
				$endauth++;
				$curchar = substr($disptext,$endauth,1); 
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
			if ($selprog == "")
			{
				$selprog = "ZZZZZZZ";
			}
			// If this  is a program listing, is this author in that program
			$tmpnum = strpos("ZZ" . $thisprog,$selprog);
			$incurrprog=0;
			if ($tmpnum > 0)
			{
				$incurrprog=1;
			}
			$j = 0;
			// For each possible program , count how many members are in that program
			while ($j < $numprogs)
			{
				if (strpos($thisprog,$progcodes[$j]) !== false)
				{
					$prgcnt[$j]++;
				}
				$j++;
			}
			if ($isauthlist or $isfulllist or ($incurrprog))
			{
				// Mark author with bold if this a non-program listing or if this author is in the program that the list is for
				$inprogcnt++;
				$disptext = str_replace($seekauth,"<b>" . $seekauth . "[" . $thisprog . "]</b>",$disptext);
			}
			else
			{
				// Mark author with bold and italics if this a program listing and this author is not in that program 
				$outprogcnt++;
				$disptext = str_replace($seekauth,"<b><i>" . $seekauth . "[" . $thisprog . "]</b></i>",$disptext);
			}

		}
	}
	$thisnumprogs = 0;
	$allinone = 0;
	$oneintra = 0;
	$j = 0;
	$marker = '';
	// Determining the marker if this is not a program listing
//	$details = '(A-' . $thispubnumauths . ")";   // Used for debugging
	while ($j < $numprogs)
	{
//		$details .= "(" . $progcodes[$j] . "-" . $prgcnt[$j] . ")";  // Used For Debuggin
		if ($prgcnt[$j] > 0)
		{
			$thisnumprogs++;
			// How many program are involved in this publication
		}
		if ($prgcnt[$j] > 1)
		{
			// There are atleast two authors for a program,  so this is an intra-programmatic (in a non-program listing)
			$oneintra++;
		}
		if ($prgcnt[$j] == $thispubnumauths)
		{
			// One Program contains all the authors, so this is not inter-programmatic  (in a non-program listing)
			$allinone = 1;
		}
		$j++;
	}
//	$details .= "(thisnumprogs-" . $thisnumprogs. ")";
//	$details .= "(AllInONe-" . $allinone . ")";
//	$details .= "(numauths-" . $thispubnumauths . ")";
	
	if ($thispubnumauths > 1)
	{
		// Can't have an inter or intra with only one member as an author
		if ($allinone)
		{
			// If all authors are form one program the publications is only intra.
			$marker = '*';
		}
		else
		{
			// mutiple authors and no program contains all the authors
			if ($numprogs > 1)
			{
				// more than one program involve mark it  as inter
				$marker = '+';
			}
			if ($oneintra > 0)
			{
				// at least one program with multiple member mark it as intra
				$marker .= '*';
			}
		}
	}
	else
	{
		//only one meber no marking
		$marker = '';
	}
	if ($isproglist)
	{
		// ok markings are now made with respect to the selected program for this listing
		$marker = '';
		if ($outprogcnt > 0)
		{
			// at least one member is outside of this program so it is inter (at least one has to in the program or this publication would have been filtered)
			$marker = '+';
		}
		if ($inprogcnt > 1)
		{
			// more than one author is in the selected program so it is intra (does not matter if the are 3 individual in a different program (Program 1) the intra markinbg sin this listing are with respect the to the program being listed) 
			$marker .= '*';
		}
//		$details .= "(outprogcnt-" . $outprogcnt . ")";
//		$details .= "(inprogcnt-" . $inprogcnt . ")";
		
	}
	if ($marker == "*")
	{
		$numintra++;
	}	
	
	if ($marker == "+")
	{
		$numinter++;
	}	
	if ($marker == "+*")
	{
		$numinter++;
		$numintra++;
		$numboth++;
	}
	// All done the intra/inter programmatic stuff
	// Now ont the easier markings
	if ($dodagger && $showif)
	{
		// IF greater than 9 but less than 10
		$marker = "&dagger;" .  " " . $marker;
	}
	if ($dodagger2 && $showif)
	{
		// IF greater than  10
		$marker = "&Dagger;" .  " " . $marker;
	}
	if ($dosect && $showinter)
	{
		// Show Inter and was Inter-Consortium
		$marker = "&sect;" .  " " . $marker;
	}
	// Pick colors if we are highliting Firsz/Senior AUthor Publications
	$rowcolor = "White";
	if ($skccfirstauth)
	{
		$rowcolor = "LightBlue";
		$numfirstauth++;
	}
	if ($skccseniorauth)
	{
		$rowcolor = "Yellow";		
		$numlastauth++;
	}
	if ($skccfirstauth && $skccseniorauth)
	{
		$rowcolor = "Orange";		
		$numfirstandlast++;
	}
	if (!$higlightfirstlast)
	{
		// For this lsit we do not care
		$rowcolor = "White";		
	}
	
	// Start row and put in marker
	echo '<tr  style="background-color:  ' . $rowcolor . ';"><td nowrap align="right" valign="top">' . $marker . " ";
	//echo $details;
	// Add number if requested
	if ($enumerate)
	{
		echo $i . '.&nbsp;&nbsp;';
	}
	echo '</td>';
	// Alter disptext as needed
	if ($pubstatus == "aheadofprint")
	{
		$disptext .= ". [Epub ahead of print] ";
	}
	if ($showpmcid)
	{
		$disptext .= " &nbsp; PMID: $pubmedid";
		if (strlen($pmcid) > 0)
		{
			$disptext .= " &nbsp; PMCID: $pmcid ";
		}
	}
	// Filter to catch bad Volume/Issue  Page number combinations
	$disptext = str_replace(":.",".",$disptext);
	// Add the Impact factor for the journal
	if ($showif)
	{
		$disptext = str_replace($journal . ".",$journal . "[" . $impact . "].",$disptext);
	}
	// Apply ONRENDER Author Filters
	// ex. replace  "Zhang JI" with "Zhang J"
	
	$filtind = 0;
	while ($filtind < $numfilts)
	{
		$disptext = str_replace($filtsrch[$filtind],$filtrepl[$filtind],$disptext);
		$filtind++;
	}

	// show the [ublications]
	echo '<td valign="top">' . str_replace("..",". ",$disptext);

	// Add a link to the abstract if requested
	if ($abstract)
	{
		echo '( <a href="http://www.ncbi.nlm.nih.gov/pubmed/' . $pubmedid . '" target="_blank">Abstract</a> )';
	}
	echo '<br><br></td>' . "\n";
	// Add Rowid and Pubdate if requested
	if (!$skipdate)
	{
		$rowidtext ='';
		if ($showrowid)
		{
			$rowidtext = "<br>ROWID: " . $rowid;
		}
		echo '<td valign="top">' . $pubdate .$rowidtext;
		if ($altsort == 3)
		{
			echo '<br /><b><i>' .  $journal . '</b></i>';
		}
		echo '</td>';
	}

	echo '</tr>';
	$i++;
}
echo '</table><br /><br />';
echo "<p>";
$numpubs = $i - 1;
$donewprogs = 0;
// Display the Program  Structure Scenario Used For This Listing
$query = "select * from systemconf where rowid = 1" ;
$result = pg_query_params($mydbh, $query,array());
if (!$result) {printf (pg_last_error($mydbh)); exit;}
$lmrow = pg_fetch_array($result);
if (!$useprop1 && !$useprop2)
{
	echo "<b>" . $lmrow["historical_program_alignment"] . "</b>";
}
if ($useprop1)
{
	echo "<b>" . $lmrow["proposed_alignment_1"] . "</b>";
}
if ($useprop2)
{
	echo "<b>" . $lmrow["proposed_alignment_2"] . "</b>";
}
echo "<br /><br />";
// Show appropriate description of total publication count
if ($isfulllist)
{
	echo "There were $numpubs publications authored by $centerabbrev Members from  $afterdate to $stoplabel.<br>";
}
if ($isproglist)
{
	echo "There were $numpubs publications authored by $centerabbrev Members in the " . $prognamelist[$prog] . "[$prog] Program from $afterdate to $stoplabel.<br>";
}
if ($isauthlist)
{
	echo "There were $numpubs publications authored by $auth as a $centerabbrev Member from  $afterdate to $stoplabel.<br>";
}

//Calculate Inter/Intra, ImpactFactor, First/Senior Author Statisitcs
if ($numpubs > 0)
{
	$percinter = sprintf("%2.2f",($numinter/$numpubs)*100);
	$percintra = sprintf("%2.2f",($numintra/$numpubs)*100);
	$percboth = sprintf("%2.2f",($numboth/$numpubs)*100);
	$perceith = sprintf("%2.2f",((($numinter + $numintra - $numboth)/$numpubs)*100));
	$percfirstauth = sprintf("%2.2f",($numfirstauth/$numpubs)*100);
	$perclastauth = sprintf("%2.2f",($numlastauth/$numpubs)*100);
	$percfirstandlast = sprintf("%2.2f",($numfirstandlast/$numpubs)*100);
}
else
{
	$percinter = "0.0";
	$percintra = "0.0";
	$percboth = "0.0";
	$perceith = "0.0";
	$percfirstauth = "0.0";
	$perclastauth = "0.0";
	$percfirstandlast = "0.0";
}
if ($denom1 > 0)
{
	$percifgt10 = sprintf("%2.2f",($numifgt10/$denom1)*100);
	$percifgt9 = sprintf("%2.2f",($numifgt9/$denom1)*100);
}
else
{
	$percifgt10 = "0.0";
	$percifgt9 = "0.0";	
}
if ($denom2 > 0)
{
	$adjpercifgt10 = sprintf("%2.2f",($numifgt10/$denom2)*100);
	$adjpercifgt9 = sprintf("%2.2f",($numifgt9/$denom2)*100);
}
else
{
	$adjpercifgt10 = "0.0";
	$adjpercifgt9 = "0.0";
}

echo "$numinter publications($percinter%) were inter-programmatic(+ or +*).<br>"; 
echo "$numintra publications($percintra%) were intra-programmatic(* or +*).<br>"; 
echo "$numboth publications($percboth%) were inter-programmatic and intra-programmatic(+*).<br>"; 
echo ($numinter + $numintra - $numboth) . " publications($perceith%) were inter-programmatic and/or intra-programmatic(+, * or +*).<br><br>";
if ($showif)
{
	echo "<b>Assuming an IF of 0 for Journals with an undefined IF</b><br />";
	echo $numifgt10 . " publications($adjpercifgt10%) had an impact factor > 10 (&dagger;).<br/>";
	echo $numifgt9 . " publications($adjpercifgt9%) had an impact factor > 9 (&dagger; or &Dagger;).<br/>";
	echo "Average Impact Factor  [$sumif/$denom2]: " . sprintf("%3.2f",($sumif/$denom2)) . "<br /><br />";

	echo "<b>Omitting Journals with an undefined IF</b><br />";
	echo $numifgt10 . " publications($percifgt10%) had an impact factor > 10 (&dagger;).<br/>";
	echo $numifgt9 . " publications($percifgt9%) had an impact factor > 9 (&dagger; or &Dagger;).<br/>";
	echo "Average Impact Factor [$sumif/$denom1]: " . sprintf("%3.2f",($sumif/$denom1)) . "<br /><br />";
	
}
if ($showinter)
{
	echo "There were $numinterinst inter-institutional publications(&sect;).<br /><br/ >";
}
if ($higlightfirstlast)
{
	echo "$numfirstauth Publications($percfirstauth%) are highlighted in Blue as a SKCC Member was the First Author <br />";
	echo "$numlastauth Publications($perclastauth%) are highlighted in Yellow as a SKCC Member was the Senior Author<br />";
	echo "$numfirstandlast Publications($percfirstandlast%) are highlighted in Orange as a SKCC Member was the First Author and a SKCC member was Senior Author (or if there was only one author)<br /><br /><br />";
}

// Describe the way members were highlighted
if ($isproglist)
{
	echo "Authors who were members during publication and in the Program are marked in bold.<br>";	
	echo "Authors who were members during publication and NOT in the Program are marked in bold and italics.<br>";		
}
else
{
	echo "Authors who were members during publication are in bold.<br>";
}

// Show the various Programs and the Codes associated with them (only program codes are displayed in the publications)
echo "The Program Numbers/Abbreviations for these authors appear in [] for each Member.<br>"; 
echo "Program Abbreviations are listed below, all publications after January of 2011 should be using the current Program Abbreviations.<br><br>";
echo "<b>Current Codes/Programs</b><br>";
echo $thisproglist;
echo "<br /><br />";
echo "<b>Alternate Codes/Programs</b><br>";
echo $othproglist;
echo "<br /><br />";
echo "<b>Older Codes/Programs</b><br>";
echo $oldproglist;
echo "<br /><br />";

if ($showquery)
{
	echo "<br /><br />Check Program Labels For Authors<br />";
	foreach ($progcheck as $thischeck => $thischeckcnt)
	{
		echo "progcheck[" . $thischeck . "]: " . $thischeckcnt . "<br />";
	}
	echo "<br /><br />";
}
// Show last modified date		
$tmpstr = $lmrow["lastmod"];
$tmparr = explode("-",$tmpstr);
$lastmod = $tmparr[1] . "/" . $tmparr[2] . "/" . $tmparr[0];

echo '<br /><br />';
echo "<i>The $centerabbrev Publication database is usually updated weekly or bi-weekly, the last update was completed on $lastmod.</i><br />";
// GRefernce the Impact Factor Source
echo '<i>Currently suing 2013/14 Journal Impact Factors from <a href="http://www.citefactor.org/journal-impact-factor-list-2014.html" target="_blank">citefactor.org</a></i>';
pg_close($mydbh);
// Decide to suppress the Header and footer for this function
//include("footer.php");
echo "</p>";
echo '</body></html>';
?>

