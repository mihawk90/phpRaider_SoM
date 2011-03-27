<?php
	// options for the user to change
	// users will have control over these variables
	$auth_option = array(
						array(
							'text'=>'Joomla URL',
							'description'=>'URL of your Joomla site',
							'variable'=>'register_url',
							'value'=>'http://'.$_SERVER['HTTP_HOST'].'/'),
						array(
							'text'=>'Joomla Path',
							'description'=>'Path to your installed Joomla, including trailing slash!',
							'variable'=>'joomla_path',
							'value'=>RAIDER_BASE_PATH.'joomla'.DIRECTORY_SEPARATOR)
					);

	// hard coded options specific to the login script
	// users will not have control over these variables
	$auth_default = array(
						'use_login'=>0
					);
?>