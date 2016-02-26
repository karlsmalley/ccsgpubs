<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require "ccsgpubs-library.php";
?>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $applicationtitle; ?> Update Publication Entry</title>
<?php include "header.php"; ?>
<table width="100%"><tr><td align="center">
<form name="restorepubs" method="post" action="restorepubs.php">
<br /><br /><font face="Arial, Helvetica, sans-serif" color="#FF0000" >Enter PubMed IDs to be restored, one ID per line<br /><br />
<textarea cols="30" rows="30"  name="pubmedids">
</textarea><br /><br />
<input type="submit" value="Remove PubMedIDs from Rejected Publications Table"><br /><br />
<a href="menu.php">Return To Main Menu</a><br /><br />
</td></tr></table>
<?php 
pg_close($mydbh);
include("footer.php");
?>