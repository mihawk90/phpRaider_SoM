<?php
	// options for the user to change
	// users will have control over these variables
	$auth_option = array(
						array(
							'text'=>'Registration URL',
							'description'=>'URL of register file',
							'variable'=>'register_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/vbulletin/register.php'),
						array(
							'text'=>'vBulletin Path',
							'description'=>'Path to your installed vBulletin forum, including trailing slash!',
							'variable'=>'vBulletin_path',
							'value'=>RAIDER_BASE_PATH.'vbulletin'.DIRECTORY_SEPARATOR),
						array(
							'text'=>'vBulletin URL',
							'description'=>'Including trailing slash!',
							'variable'=>'vBulletin_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/vbulletin/'),
					);

	// hard coded options specific to the login script
	// users will not have control over these variables
	$auth_default = array(
						'use_login'=>0
					);
?>
