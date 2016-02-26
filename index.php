<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
// Cancer Center Support Grant Publication Application
// 
// Written by Karl J. Smalley of the Sidney Kimmel Cancer Center at Thomas Jefferson University
//
// This application allows users to download publications from pubmed, assign cancer Center Members and
// associated program to the publication. The publication rendering script will then display the publication
// highlight Cancer Center members and inserting the appropriate program codes. It will also mark the
// publication intra- or inter-programmatic depending on the programs of the members and the scope of the display.
//
// If John Doe and Sally Williams are in Program P1 and John Smith is in Program P2 and Program[P1]]and they collaborated on  a publication
// the publication rendering script would display it like this for a general Center Publication Listing:
//
//  * 1. <b>Doe J[P1]</b>, <b>Williams S[P1]</b>, <b>Smith J[P1,P2]</b>. Title of Article. Journal[JIF]. Year Mon; volume(issue); pages.   PMID: 9999999   PMCID: PMC121212
//
//  It is marked intra-programmatic because 3 members of P1 are involved. It is not marked inter-programmatic(+) because all the members are from one program. 
//
//  In a listing for P1, it would be rendered the same (all members bold and only marked intra-programmatic):
//
//  In a listing for P2 it would be rendered this way:
//
//  + 1. <i><b>Doe J[P1]</b></i>, <i><b>Williams S[P1]</b></i>, <b>Smith J[P1,P2]M.</b>. Title of Article. Journal[JIF]. Year Mon; volume(issue); pages.   
//        PMID: 9999999   PMCID: PMC121212
//
// Non P2 members are now italicized and the intra/inter markings are form the perspective of this program. It involves only  one member of P1 so it is not intra-programmatic.
// it involves members form program other than P1 so it is inter-programmatic.
//
// ------------------- Alternate Program Structure -----
// If John Smith was only in Program P2, it would be rendered like this for a general listing:
//
//  +* 1. <b>Doe J[P1]</b>, <b>Williams S[P1]</b>, <b>Smith J[P2]</b>. Title of Article. Journal[JIF]. Year Mon; volume(issue); pages.   PMID: 9999999   PMCID: PMC121212
//
// It is marked intra-programmatic because there is at least one program with multiple authors. 
// It is marked inter-programmatic because all of the members do not all come form one program
//
// For a P1 listing the publication would be render like this: 
//
//  +* 1. <b>Doe J[P1]</b>, <b>Williams S[P1]</b>, <i><b>Smith J[P2]</b><i>. Title of Article. Journal[JIF]. Year Mon; volume(issue); pages.   PMID: 9999999   PMCID: PMC121212
//
// It is marked intra-programmatic because there are more than 1 P1 members as authors. 
// It is marked inter-programmatic because ther is at least one non-P1 member as an author (who will be italicized) as well.
//
// For a P2 listing the publication would be render like this: 
//
//  + 1. <i><b>Doe J[P1]</b></i>, <i><b>Williams S[P1]</b></i>, <b>Smith J[P2]</b>. Title of Article. Journal[JIF]. Year Mon; volume(issue); pages.   PMID: 9999999   PMCID: PMC121212
//
// It is not marked intra-programmatic because there is only 1 P2 member as an author. 
// It is marked inter-programmatic because therw is at least one non-P2 member as an author (who will be italicized) as well.

require "ccsgpubs-library.php";
pg_close($mydbh);
?>

<html>
<head>
<title><?php echo $applicationtitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php include "header.php"; ?>
<div align="center">
<br />
<font color="#3366CC" size="4" face="Arial, Helvetica, sans-serif" ><strong><?php echo $applicationtitle; ?></strong></font>
<hr width="900" size="1" noshade>
<form name="SignIn" method="post" action="menu.php">
    <table align="left" border="0" width="700" >
      <tr> 
        <td valign="top"><p><img src="images/loginpic.jpg" hspace="10" vspace="5"></p></td>
        <td valign="top" align="center">
				<table>
				<tr>
						<td align="right"><font face="Arial, Helvetica, sans-serif">CampusKey: </font></td>
            <td align="left"><input name="campuskey" type="text" size="25"></td>
						</tr><tr>
						<td align="right"><font face="Arial, Helvetica, sans-serif">Password: </font></td>
            <td align="left"><input name="passwd" type="password" id="password"></td>
						</tr><tr>
						<td colspan="2" align="center"><input type="submit" name="Submit" value="Login"></td>
						</tr><tr>
						<td colspan="2" align="center">
					<p><font color="#3366CC" size="3" face="Arial, Helvetica, sans-serif" >You must authenticate yourself using your campuskey and password.<br>You must also be registered to use this application.</td>
      </tr>
			</table>
			</td></tr>
    </table>
</form>
</div><br clear="all" />
<?php include "footer.php"; ?>
</body>
</html>
