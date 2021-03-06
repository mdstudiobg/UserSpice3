<?php
/*
UserSpice 3
by Dan Hoover at http://UserSpice.com
Major code contributions by Astropos

a modern version of
UserCake Version: 2.0.2


UserCake created by: Adam Davis
UserCake V2.0 designed by: Jonathan Cassels




*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Log the user out
if(isUserLoggedIn())
{
	$ip = getIP();
	writeAudit($loggedInUser->user_id,$ip,$loggedInUser->user_id,2,lang("SIGNOUT_AUDITTEXT",""));

	$loggedInUser->userLogOut();
}

if(!empty($websiteUrl))
{
	$add_http = "";

	if(strpos($websiteUrl,"http://") === false)
		{
			$add_http = "http://";
		}

		header("Location: ".$add_http.$websiteUrl);
		die();
	}
	else
	{
		header("Location: login.php");
		die();
	}

	?>
