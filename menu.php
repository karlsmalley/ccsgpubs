<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
//phpinfo();
require "ccsgpubs-library.php";
?>
<html>
 <head>
<SCRIPT LANGUAGE="Javascript">
function dopub_update()
{
	el = document.getElementById("modal2");
	el2 = document.getElementById("trans2");
	el3  = document.getElementById("pubreload");
	var rect = document.body.getBoundingClientRect ();
	var h = rect.bottom - rect.top;
	var w = window.innerWidth;
	var h2 = window.innerHeight;
	el.style.height =  h+"px";
	el2.style.height =  h+"px";
	el3.style.width = "800px";
	var leftm =  parseInt((w -800)/2);
	el3.style.top = "100px"
	el3.style.left=  leftm + "px"
	//alert("modal divs rezied to " + h);

	el.style.visibility =  "visible";
	scroll(0,0);
//	document.body.className = "stop-scrolling";
//	document.modalternate.alternname.focus();
}
function launch_findpubs()
{
	el = document.getElementById("modal");
	el2 = document.getElementById("trans");
	el3  = document.getElementById("findpubsquery");
	var rect = document.body.getBoundingClientRect ();
	var h = rect.bottom - rect.top;
	var w = window.innerWidth;
	var h2 = window.innerHeight;
	el.style.height =  h+"px";
	el2.style.height =  h+"px";
	el3.style.width = "800px";
	var leftm =  parseInt((w -800)/2);
	el3.style.top = "100px"
	el3.style.left=  leftm + "px"
	//alert("modal divs rezied to " + h);

	el.style.visibility =  "visible";
	scroll(0,0);
//	document.body.className = "stop-scrolling";
//	document.modalternate.alternname.focus();
}
</SCRIPT>
 <title><?php echo $applicationtitle; ?></title>
<?php include "header.php";?>
<table align="left" border="0" width="900" >
  <tr> 
   <td rowspan="2"><p><img src="images/loginpic.jpg" hspace="10" vspace="5"></p></td>
   <td valign="top" align="center"><br><h1><?php echo $applicationtitle; ?></h1><br />
	<font face="Arial, Helvetica, sans-serif" color="#3366CC" ><b>Welcome <?php echo $firstname . " " . $lastname; ?></b><br></center><left>
    <hr>
	</td></tr>
	<tr><td>
<?php
if ($usertype == "ADMIN")
{
?>
	 <a href="modusers.php">Manage Users</a><br><br>
 	 
<?php
}
?>
 	 <a href="modcentname.php">Modify Cancer Center Name/Last Update Date</a><br><br>
 	 <a href="modprograms.php">Modify Cancer Center Programs</a><br><br>
	 <a href="modmembers.php">Modify Cancer Center Members</a><br><br>
	 <a href="modpublications.php">Modify Already Loaded Publications</a><br><br>
	 <a href="unreject.php">Remove Publications From Rejection Listing</a><br><br>
	 <a href="javascript:launch_findpubs();">Bulk Load Member Publications From PubMed</a><br><br>
	 <a href="javascript:dopub_update();">Update Publications (reloads publication data)</a><br><br>
	 <a href="uidsbyhand.php">Add Publications Using A List Of PubMed IDs</a><br><br>
 	 <a href="pubmenu.php" target="_blank">Publication Display Menu</a><br><br>
	 <a href="./index.php">Logout</a><br><br>



</td></tr></table><br clear="all" />
<?php
pg_close($mydbh);
include "footer.php"; 
?>
<div id="modal" style="position: fixed; top: 0px; left: 0px; z-index: 1000; visibility: hidden; width: 100%; height: 100%;">
<div id="trans" style="filter:alpha(opacity = 80); opacity: 0.8; position: absolute; top: 0px; left: 0px; z-index: 1010; width: 100%; height: 100%; background-color: gray;">
</div>
<div id="findpubsquery" style="text-align: center; z-index: 1020; position: absolute; top: 100px; left:400px; width: 800px; height: 600px; background-color: #D5FFFF;">
<form name="findpubs" action="findpubs.php" method="post" >
<input type="hidden" name="nummonths">
<br /><br /><table width="100%">
<tr><td align="left">This Routine Will Search For Publication s For All Members.<br />
A Count of New Publications (not already stored in the publication table or rejection Table) will be presented.<br />
Clicking on this count will launch a new window (please make sure any pop-up blockers are off) with the list of new publications.<br />
You will have the ability to Accept or Reject Each publication.<br />
If publications are saved, you will be given the option to modify the Author/Programs affiliated with those publications.<br />
At the completion of this process you should close the launched window and proceed to the next member with new publications.<br />
If you make a mistake and reject a publication you can remove it form the list using the "Remove Publications From Rejection Listing" option in the main menu.<br />
You can then add the mistakenly rejected publication by using bulk load option(not recommended) or the "Add Publications Using A List Of PubMed IDs" option.<br /><br />
<b>Enter How Far Back To Search For New Publications(# Of Months):</b>
<input type="text" name="nummonths" value="3"> months<br /><br />
</td></tr>
<tr><td align="center">
<input type="submit" value="Begin Bulk Load Process"><br />
<a href="menu.php">Return To Main Menu</a></form>
</td></tr></table>
</div>
</div>
<div id="modal2" style="position: fixed; top: 0px; left: 0px; z-index: 1000; visibility: hidden; width: 100%; height: 100%;">
<div id="trans2" style="filter:alpha(opacity = 80); opacity: 0.8; position: absolute; top: 0px; left: 0px; z-index: 1010; width: 100%; height: 100%; background-color: gray;">
</div>
<div id="pubreload" style="text-align: center; z-index: 1020; position: absolute; top: 100px; left:400px; width: 800px; height: 600px; background-color: #D5FFFF;">
<form name="pubreloadform" action="pubreload.php" method="post" >
<input type="hidden" name="nummonths">
<br /><br /><table width="100%">
<tr><td align="left">This Routine Will Update Existing Publication Records.<br />
Usually this means no change, except for page numbering and potentially the publication date (print date is preferred over e-publish date by most journals.) Occasionally a publication will be removed. In this case the publication will be updated with a message indicating the publication was not found. Before doing the update all publication display text is backed up to another field. You will be able to see what the former publication looked like and decide whether to look into the matter further or just delete the publication that no longer exists in PubMed.<br /><br />
<b>Enter How Far Back To Update Existing Publication Records(# Of Months):</b>
<input type="text" name="nummonths" value="6"> months<br /><br />
</td></tr>
<tr><td align="center">
<input type="submit" value="Begin Update Process"><br />
<a href="menu.php">Return To Main Menu</a></form>
</td></tr></table>
</div>
</div>
</body>
</html>
