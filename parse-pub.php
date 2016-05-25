<?php
//echo "in parser<br />";
$charmonths["Jan"] = 1;
$charmonths["Feb"] = 2;
$charmonths["Mar"] = 3;
$charmonths["Apr"] = 4;
$charmonths["May"] = 5;
$charmonths["Jun"] = 6;
$charmonths["Jul"] = 7;
$charmonths["Aug"] = 8;
$charmonths["Sep"] = 9;
$charmonths["Oct"] = 10;
$charmonths["Nov"] = 11;
$charmonths["Dec"] = 12;
$charmonths[""] = 0;

$year = null;
$dateofpub = null;
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

$filtquery = "select * from filters where filter_when = 'PRELOAD' ";
$filresult = pg_query_params($mydbh,$filtquery,array());
if (!$filresult) {printf (pg_last_error($mydbh)); exit;}

$numfilts = 0;
while($fitlrow = pg_fetch_array($filresult) )
{
	$filtsrch[$numfilts]  = $fitlrow["searchtext"];
	$filtrepl[$numfilts]  = $fitlrow["replacetext"];
	$numfilts++;
}
$isjournal = 0;
$url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=PubMed&tool=$toolnameforncbi&email=$emailforncbi&id=" . $pubid . "&rettype=sgml";
//		print "opening $url<br><br>";
$url2 = "http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=PubMed&tool=$toolnameforncbi&email=$emailforncbi&list_uids=" . $pubid . "&dopt=ABSTRACT";

$handle = fopen ($url, "r");
$retry =0;
while (!$handle && $retry < 3)
{
	sleep(5);
	$handle = fopen ($url, "r");
	$retry++;
}
if ($retry == 3 and !$handle)
{
	echo  "Error retrieving PubMed Entry for PubMedId: $pubid ...";
	$foundpubok=0;
}
$buffer = "";
while($thisline = fgets($handle))
{
	$buffer .= $thisline;
}
$orig_xml = $buffer;
$updated_xml = $buffer;
	
$pubxml = @simplexml_load_string($buffer);
if (!$pubxml)
{
	echo "Unable to load Publication . . .";
	$disptext =  "Unable To Find Publication Data For PubMedId: $pubid ";
	$foundpubok=0;

}
else
{
	//echo "<br />";
	//echo "url: $url<br /><br />";
	//var_dump($pubxml);
	if (isset($pubxml->PubmedArticle))
	{
		$isjournal = 1;
		$PMID = $pubxml->PubmedArticle->MedlineCitation->PMID;
		$alt_issn = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->ISSN;
		$journal_issn = $pubxml->PubmedArticle->MedlineCitation->MedlineJournalInfo->ISSNLinking;
		if (strlen(trim($journal_issn))== 0)
		{
			$journal_issn = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->ISSN;
		}
		if (strlen(trim($journal_issn))== 0)
		{
			$journal_issn = "-99";
		}
		$foundpubdate  = 0;
		if (isset($pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate))
		{
			$foundpubdate = 1;
			if (isset($pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->Year))
			{
				$yearofpub = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->Year;
				$monthofpub = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->Month;
				$dayofpub = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->Day;
				$dispdate = $yearofpub . " " . $monthofpub . " " . $dayofpub;
				$dispdate = str_replace("  "," ",$dispdate);
				
				if ($yearofpub != "")
				{
					$year = $yearofpub;
				}
				if ($dayofpub == "")
				{
					$getdateadded = 1;
					$dayofpub = "1";
				}
				if ($monthofpub == "")
				{
					$getdateadded = 1;
					$monthofpub = "Jan";
				}
				$monthofpub=trim($monthofpub);
				$tmpnum = $monthofpub*1;
				if ($tmpnum > 0)
				{
					$tmpstr = $tmpnum;
				}
				else
				{
					if (isset($charmonths[$monthofpub]))
					{
						$tmpstr = $charmonths[$monthofpub];
					}
					else
					{
						$tmpstr = "1";
					}
				}
				$monthnum  = $tmpstr*1;
			}
			elseif (isset($pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->MedlineDate))
			{
				$dispdate = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->MedlineDate;					
				$temparr = explode(" ",$dispdate);
				$year = $temparr[0];
				$yearofpub = $year;
				$cmonth = "";
				if(isset($temparr[1]))
				{
						$cmonth = substr($temparr[1],0,3);
				}
				if (isset($temparr[2]))
				{
					$daystr = $temparr[2];
					$temparr2 = explode("-",$daystr);
					$dayofpub = $temparr2[0];
					if ($dayofpub == 0)
					{
						$dayofpub = 1;
					}
				}
				//		echo "<br>";
				$tmpnum = $cmonth*1;
				if ($tmpnum > 0)
				{
					$monthnum = $tmpnum;
				}
				else
				{
					$monthnum = $charmonths[$cmonth];
				}
				if (($monthnum*1) == 0)
				{
					$monthnum = 1;
				}
			}
			if (!isset($dayofpub))
			{
				$dayofpub = 1;
			}
			
			$dateofpub = $monthnum . "/". $dayofpub . "/" . $yearofpub;

		}

		$altdateofpub = null;
		$altdispdate = null;
		$foudnaltpubdate = 0;
		if (isset($pubxml->PubmedArticle->MedlineCitation->DateCreated))
		{
			$foudnaltpubdate = 1;
			$ayearofpub = $pubxml->PubmedArticle->MedlineCitation->DateCreated->Year;
			$amonthofpub = $pubxml->PubmedArticle->MedlineCitation->DateCreated->Month;
			$adayofpub = $pubxml->PubmedArticle->MedlineCitation->DateCreated->Day;
			$altdispdate = trim($ayearofpub . " " . $amonthofpub . " " . $adayofpub);
			$altdateofpub = null;
			if ($ayearofpub*1 > 0)
			{
				if ($amonthofpub*1 == 0)
				{
					$amonthofpub = 1;
				}
				if ($adayofpub*1 == 0)
				{
					$adayofpub = 1;
				}
				$altdateofpub = $amonthofpub  . "/" . $adayofpub . "/" . $ayearofpub ;
			}
		}
		if (!$foundpubdate)
		{
			if ($foundfoudnaltpubdate)
			{
				$dispdate = $altdispdate;
				$dateofpub = $foudnaltpubdate;
			}
			else
			{
				$dispdate = "";
				$dateofpub = null;
			}

		}
		$PublicationStatus = null;
		if (isset($pubxml->PubmedArticle->PubmedData->PublicationStatus))
		{
			$PublicationStatus = $pubxml->PubmedArticle->PubmedData->PublicationStatus;
		}


		$idind = 0;
		foreach ($pubxml->PubmedArticle->PubmedData->ArticleIdList->ArticleId as $thisid) 
		{
			$idtype = $thisid->attributes()->IdType;
			if ($idtype == "pmc")
			{
				$pmcid = $pubxml->PubmedArticle->PubmedData->ArticleIdList->ArticleId[$idind];
			}
			if ($idtype == "doi")
			{
				$doi = $pubxml->PubmedArticle->PubmedData->ArticleIdList->ArticleId[$idind];
			}
			if ($idtype == "pii")
			{
				$pii = $pubxml->PubmedArticle->PubmedData->ArticleIdList->ArticleId[$idind];
			}
			$idind++;
			
		}


		
		$ArticleTitle = $pubxml->PubmedArticle->MedlineCitation->Article->ArticleTitle;
		//echo "ArticleTitle; $ArticleTitle<br />";
		$MedlinePgn = null;
		if (isset($pubxml->PubmedArticle->MedlineCitation->Article->Pagination->MedlinePgn))
		{
			$MedlinePgn = $pubxml->PubmedArticle->MedlineCitation->Article->Pagination->MedlinePgn;
		}

		$Volume = null;
		if (isset($pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Volume))
		{
			$Volume = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Volume;
		}


		$Issue = null;
		
	//	$Issue = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Issue;
		if (isset($pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Issue))
		{
			$Issue = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Issue;
		}

		
		$JournalTitle = null;
		if (isset($pubxml->PubmedArticle->MedlineCitation->Article->Journal->Title))
		{	
			$JournalTitle = $pubxml->PubmedArticle->MedlineCitation->Article->Journal->Title;
		}

		
		$MedlineTA = null;
		if (isset($pubxml->PubmedArticle->MedlineCitation->MedlineJournalInfo->MedlineTA))
		{	
			$MedlineTA = $pubxml->PubmedArticle->MedlineCitation->MedlineJournalInfo->MedlineTA;
		}
		
		$regauth = "";
		$fullauth = "";
		$Affiliations = "";
		$authandaffil = "";

		if (isset($pubxml->PubmedArticle->MedlineCitation->Article->AuthorList))
		{
			$comma = "";
			$bar = "";
			$curcolor = "green";
			foreach ($pubxml->PubmedArticle->MedlineCitation->Article->AuthorList->Author as $thisauth)
			{

				$lastname = trim($thisauth->LastName);
				if (strlen(trim($lastname)) > 0)
				{
								

					$initials= trim($thisauth->Initials);
					$firstname =  trim($thisauth->FirstName);
					if ($firstname == "")
					{
						$firstname =  trim($thisauth->ForeName);
					}
					if ($firstname == "")
					{
						$firstname =  $initials;
					}
					
					$regauth .= $comma . $lastname . " " . $initials;
					$fullauth .= $comma . $firstname . " " . $lastname;
					$thisaffil = null;
					if (isset($thisauth->AffiliationInfo->Affiliation))
					{
						$thisaffil = trim($thisauth->AffiliationInfo->Affiliation);
						$authandaffil .= $comma . '<font color="' . $curcolor . '">' .  $firstname . " " . $lastname . ' of ' . $thisaffil . '</font>';
						$Affiliations .= $bar . $thisaffil;
						if ($curcolor == "green")
						{
							$curcolor = "blue";
						}
						else
						{
							$curcolor = "green";
						}
						
						$bar = " | ";
					}
					else
					{
						$authandaffil .= $comma . $firstname . " " . $lastname;
					}
					$comma = ", ";
				}
				else
				{	
					$collname =  trim($thisauth->CollectiveName);
					if (strlen(trim($collname)) > 0)
					{	
						$regauth .= $comma . $collname;
						$fullauth .= $comma . $collname;
						$thisaffil = null;
						if (isset($thisauth->AffiliationInfo->Affiliation))
						{
							$thisaffil = trim($thisauth->AffiliationInfo->Affiliation);
							$authandaffil .= $comma . '<font color="' . $curcolor . '">' .  $collname . ' of ' . $thisaffil . '</font>';
							$Affiliations .= $bar . $thisaffil;
							if ($curcolor == "green")
							{
								$curcolor = "blue";
							}
							else
							{
								$curcolor = "green";
							}
							$bar = " | ";
						}
						else
						{
							$authandaffil .= $comma . $collname;
						}
						$comma = ", ";
					}	
				}
				
			}
			// Apply PRELOAD Author Filters
			// ex. replace  "Jiménez" with "Jimenez" and "Hajnóczky" with "Hajnoczky"
			$filtind = 0;
			while ($filtind < $numfilts)
			{
				$regauth = str_replace($filtsrch[$filtind],$filtrepl[$filtind],$regauth);
				$fullauth = str_replace($filtsrch[$filtind],$filtrepl[$filtind],$fullauth);
				$filtind++;
			}
			$regauth = trim($regauth);
			$fullauth = trim($fullauth);


		}
		

		$so = $MedlineTA . ". " . trim($dispdate);
		if ($Volume != "")
		{
			$so .= "; " . $Volume;
		}
		if ($Issue != "")
		{
			$so .= "(" . $Issue . ")";
		}
		if ($MedlinePgn != "")
		{
			$so .= ":" . $MedlinePgn;
		}
		$so = str_replace("  "," ",$so);
		$so = trim($so);
	//			echo "source: $so\n";

		$disptext = $regauth . ". " . $ArticleTitle . " " . $so . ".";
		
	}
	else
	{
		$disptext = "This is Not A Journal Article (Chapter in book or something else) -- not currently loading these.";

	/*
		$PMID = $pubxml->PubmedArticle->BookDocument->PMID;
		$publishers = $pubxml->PubmedArticle->BookDocument->Book->PublisherLocation;
		$publishers = " " . $pubxml->PubmedArticle->BookDocument->Book->PublisherName;
		$publishers = trim($publishers)
		$booktitle = $pubxml->PubmedArticle->BookDocument->Book->BookTitle ;
		$chaptitle = $pubxml->PubmedArticle->BookDocument->ArticleTitle ;
	*/	
		
	}
}
?>