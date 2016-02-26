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
<form name="savebyuids" method="POST" action="savenewpubsbyuids.php">
Enter PubMed IDs to be restored, one ID per line:<br>
<textarea name="uidlist" cols="30" rows="30"></textarea><br /><br />
<input type="submit" value="Add Publications To Publication Table"><br /><br />
</td></tr></table>
<?php 
pg_close($mydbh);
include("footer.php");
?>