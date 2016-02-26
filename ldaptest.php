<?php
#### DEFINE APPLICATION WIDE GLOBALS #####
session_start();
date_default_timezone_set('America/New_York');

$sleeptime =5;
$user = "kjs102";
$pass = "Ryan4602";
//$ldapconfig['host'] = 'jds.jefferson.edu';
//$ldapconfig['port'] = 389;
$ldapconfig['host'] = 'ldaps://jds.jefferson.edu';
$ldapconfig['port'] = 636;
$ldapconfig['basedn'] = 'ou=people,dc=jefferson,dc=edu';
$ldapconfig['authrealm'] = 'My Realm';
$i = 0;
$successes = 0;
$connfails = 0;
$bindfails = 0;
while ($i < 1000)
{
	$i++;
	$retval = authenticate($user,$pass);
	if ($retval === true)
	{
		$successes++;
	}
//	if ($retval == -99)
//	{
//		$connfails++;
//	}
//	if ($retval == -1)
	else
	{
		$bindfails++;
	}
//	if (($i % 10) == 0 || $i == 1)
//	{
		echo "$successes Successful Logins in $i attempts (" . number_format($successes/$i*100) . "%)  connection fails: $connfails   bindfails: $bindfails\n";
//	}
	
	sleep($sleeptime);
}
echo "$successes Successful Logins in $i attempts (" . number_format($successes/$i*100) . "%)\n";

function authenticate($PHP_AUTH_USER,$PHP_AUTH_PW) {
    global $ldapconfig;
    echo "campuskey: $PHP_AUTH_USER<br>";
  //  echo "password: $PHP_AUTH_PW<br>";
    print_r($ldapconfig);

	
    if ($PHP_AUTH_USER != "" && $PHP_AUTH_PW != "") 
	{
        $ds=ldap_connect($ldapconfig['host'],$ldapconfig['port']);
		if (!$ds)
		{
			return false;
		}
		if (ldap_bind( $ds, "uid=" . $PHP_AUTH_USER . ",ou=people,dc=jefferson,dc=edu", $PHP_AUTH_PW) )
		{
				return true;
		}
		else
		{
			return false;
		}
                  
	}
return false;
}

?>
