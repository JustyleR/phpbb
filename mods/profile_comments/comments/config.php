<?php

require('../config.php');

$conn = mysqli_connect($dbhost, $dbuser, $dbpasswd);
		mysqli_select_db($conn, $dbname);

$uninstall_key = "";