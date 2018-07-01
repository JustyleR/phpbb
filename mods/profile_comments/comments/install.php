<?php

// The Install script

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
	<input type="submit" name="install" value="Install" />
</form>
';

if(isset($_POST['install'])) {
	
	$uninstall	= $_POST['uninstall_key'];
		
	require('config.php');
	
	$sql = "CREATE TABLE `profile_comments` (
	  `comment_id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	  `user_id` int(11) NOT NULL,
	  `author_id` int(11) NOT NULL,
	  `comment` text COLLATE utf8_unicode_ci NOT NULL,
	  `date` varchar(100) COLLATE utf8_unicode_ci NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	
	mysqli_query($conn, $sql);
	
	if(file_exists('config.php')) {
		
		// Open the config file and replace the empty space with the key
		$content = file_get_contents('config.php');
		$content = str_replace('$uninstall_key = "";', '$uninstall_key = "'. $uninstall .'";', $content);
		file_put_contents('config.php', $content);
		
		// Get the currect style that is active
		$getStyle = mysqli_query($conn, "SELECT style_path FROM ". $table_prefix ."styles WHERE style_active='1'");
		if(mysqli_num_rows($getStyle) > 0) {
			
			// Open the memberlist_view.html and add the link
			$style		= mysqli_fetch_assoc($getStyle)['style_path'];
			$content	= file_get_contents('../styles/'. $style .'/template/memberlist_view.html');
			$content	= str_replace(
			'<!-- IF U_SWITCH_PERMISSIONS --> [ <a href="{U_SWITCH_PERMISSIONS}">{L_USE_PERMISSIONS}</a> ]<!-- ENDIF -->',
			'<!-- IF U_SWITCH_PERMISSIONS --> [ <a href="{U_SWITCH_PERMISSIONS}">{L_USE_PERMISSIONS}</a> ]<!-- ENDIF -->
			[ <a href="comments/index.php?p=home&u={USERNAME}">View Comments</a> ]',
			$content);
			file_put_contents('../styles/'. $style .'/template/memberlist_view.html', $content);
		}
		
	} else {
		
		echo 'The file <strong>config.php</strong> is missing!!';
		
	}
	
	echo 'Done!<br />
	Please delete the cache!';
	
}