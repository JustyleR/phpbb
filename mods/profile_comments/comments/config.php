<?php

// Profile Comments mod by JustyleR
// Config file

if(!defined('file_access')) { exit(); }

require('../config.php');

$conn = mysqli_connect($dbhost, $dbuser, $dbpasswd);
		mysqli_select_db($conn, $dbname);
		
// Settings
$uninstall_key 		= "";
$comments_per_page	= 4; // How many comments will be shown on 1 page