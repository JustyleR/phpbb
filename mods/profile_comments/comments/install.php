<?php

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
		
		$content = file_get_contents('config.php');
		$content = str_replace('$uninstall_key = "";', '$uninstall_key = "'. $uninstall .'";', $content);
		file_put_contents('config.php', $content);
		
		$getStyle = mysqli_query($conn, "SELECT style_path FROM ". $table_prefix ."styles WHERE style_active='1'");
		if(mysqli_num_rows($getStyle) > 0) {
			
			$style		= mysqli_fetch_assoc($getStyle)['style_path'];
			$content	= file_get_contents('../styles/'. $style .'/template/memberlist_view.html');
			$content	= str_replace(
			'<!-- IF U_SWITCH_PERMISSIONS --> [ <a href="{U_SWITCH_PERMISSIONS}">{L_USE_PERMISSIONS}</a> ]<!-- ENDIF -->',
			'<!-- IF U_SWITCH_PERMISSIONS --> [ <a href="{U_SWITCH_PERMISSIONS}">{L_USE_PERMISSIONS}</a> ]<!-- ENDIF -->
			[ <a href="comments/profile.php?u={USERNAME}">View Comments</a> ]',
			$content);
			file_put_contents('../styles/'. $style .'/template/memberlist_view.html', $content);
		}
		
		$content = file_get_contents('config.php');
		$content = str_replace('$uninstall_key = "";', '$uninstall_key = "'. $uninstall .'";', $content);
		file_put_contents('config.php', $content);
		
	} else {
		
		echo 'The file <strong>config.php</strong> is missing!!';
		
	}
	
	echo 'Done!<br />
	Please delete the cache!';
	
}