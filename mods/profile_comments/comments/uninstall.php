<?php

// The Uninstall script

define('file_access', TRUE);
define('IN_PHPBB', true);
define('ROOT_PATH', "../");

if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) { exit(); }

$phpEx = "php";
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
include($phpbb_root_path . 'common.' . $phpEx);

$request->enable_super_globals();

echo '
<form action="" method="POST">
	<input type="text" name="uninstall_key" placeholder="Uninstall Key" /><br />
	<input type="submit" name="uninstall" value="Uninstall" />
</form>
';

if(isset($_POST['uninstall'])) {
	
	$uninstall	= $_POST['uninstall_key'];
	
	require('config.php');
	
	if($uninstall == $uninstall_key) {
		
		// Drop table
		mysqli_query($conn, "DROP TABLE profile_comments");
		
		// Delete the profile link
		$getStyle = mysqli_query($conn, "SELECT style_path FROM ". $table_prefix ."styles WHERE style_active='1'");
		if(mysqli_num_rows($getStyle) > 0) {
			
			$style		= mysqli_fetch_assoc($getStyle)['style_path'];
			$content	= file_get_contents('../styles/'. $style .'/template/memberlist_view.html');
			$content	= str_replace(
			'[ <a href="comments/index.php?p=home&u={USERNAME}">View Comments</a> ]', '', $content);
			$content	= str_replace('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>','',$content);
			$content	= str_replace('<div id="last-comments"></div>','',$content);
			$content	= str_replace('<script>','',$content);
			$content	= str_replace("$.urlParam = function(name){
	var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	if (results==null){
	   return null;
	}
	else{
	   return decodeURI(results[1]) || 0;
	}
}","",$content);
			$content	= str_replace("var user = $.urlParam('u');","",$content);
			$content	= str_replace('$("#last-comments").load( "comments/last.php?u=" + user);','',$content);
			$content	= str_replace('</script>','',$content);
			file_put_contents('../styles/'. $style .'/template/memberlist_view.html', $content);
		}
		
		echo 'Deleted!<br />
		Please delete the cache!';
		
	} else {
		
		echo 'Incorrect Key!';
		
	}
	
}