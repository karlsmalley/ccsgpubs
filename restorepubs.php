<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
?>
<html>
<head>
<title><?php echo $applicationtitle; ?> - Save Publications Updates</title>
<?php
include "header.php";
?>
<table width="100%"><tr><td align="center"><br /><br />
<?php
$pubmedids = "";
if (isset($_POST['pubmedids']) && strlen(trim($_POST['pubmedids'])) > 0)
{
	$pubmedids = trim($_POST['pubmedids']);
}
$idarray = explode("\n",$pubmedids);
if (sizeof($idarray) == 0)
{
?> 
	<font face=arial size=3 color=red ><b>PubMed ID List Not Found Or Empty  -- rejections Table Not Modified</b></font><br><br>
	</body></html>
<?php
	include('footer.php');
	pg_close($mydbh);
	exit();
}
echo '<font face="Arial, Helvetica, sans-serif" color="#000000" >';
foreach($idarray as $thisid)
{
	if (strlen(trim($thisid)) > 0)
	{
		$numid = trim($thisid)*1;
		if ($numid > 0)
		{
			$restquery = "delete from rejpubs where pubmedid = $1";
			$restresult = pg_query_params($mydbh, $restquery,array($numid ));
			if (!$restresult) {printf (pg_last_error($mydbh)); exit;}

			if (pg_affected_rows($restresult) == 1)
			{
				echo "1 row ";
			}
			else
			{
				echo  pg_affected_rows($restresult) ." rows ";
			}
			echo "with a PubMedID of $numid were removed form the rejection table.<br />";
		}
		else
		{
			echo "No attempt was made to remove the suppled PubMed ID of $thisid from the rejections table.<br />";
		}
	}
}
echo '<br /><br /><font face="Arial, Helvetica, sans-serif" color="#FF0000" >The PubMed ID List Has Been Processsed.<br><br>';
echo '</font><a href="menu.php">Return to Main Menu</a><br />';
echo '</td></tr></table>';	
include('footer.php');
pg_close($mydbh);
?>

</body>
</html>
