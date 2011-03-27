<?php
	// options for the user to change
	// users will have control over these variables
	$auth_option = array(
						array(
							'text'=>'Registration URL',
							'description'=>'URL of register file',
							'variable'=>'register_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/smf/index.php?action=register'),
						array(
							'text'=>'SMF Path',
							'description'=>'Path to your installed SMF forums, including trailing slash!',
							'variable'=>'smf_path',
							'value'=>RAIDER_BASE_PATH.'smf'.DIRECTORY_SEPARATOR),
						array(
							'text'=>'SMF URL',
							'description'=>'Including trailing slash!',
							'variable'=>'smf_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/smf/'),
					);

	// hard coded options specific to the login script
	// users will not have control over these variables
	$auth_default = array(
						'use_login'=>0
					);
?>