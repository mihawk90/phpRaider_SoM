<?php
	// options for the user to change
	// users will have control over these variables
	$auth_option = array(
						array(
							'text'=>'Registration URL',
							'description'=>'URL of register file',
							'variable'=>'register_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/phpbb/profile.php?mode=register'),
						array(
							'text'=>'phpBB Path',
							'description'=>'Path to your installed phpBB2 forum, including trailing slash!',
							'variable'=>'phpbb_path',
							'value'=>RAIDER_BASE_PATH.'phpbb'.DIRECTORY_SEPARATOR),
						array(
							'text'=>'phpBB URL',
							'description'=>'Including trailing slash!',
							'variable'=>'phpbb_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/phpbb/'),
					);

	// hard coded options specific to the login script
	// users will not have control over these variables
	$auth_default = array(
						'use_login'=>0
					);
?>