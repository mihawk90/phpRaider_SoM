<?php
	// options for the user to change
	// users will have control over these variables
	$auth_option = array(
					array(
						'text'=>'Registration URL',
						'description'=>'URL of register file',
						'variable'=>'register_url',
						'value'=>'index.php?option=com_register')
					);

	// hard coded options specific to the login script
	// users will not have control over these variables
	$auth_default = array('use_login'=>1);
?>