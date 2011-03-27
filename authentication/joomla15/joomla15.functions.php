<?php
/***************************************************************************
 *                           joomla.functions.php
 *                           --------------------
 *   begin                : October 24, 2007
 *   developer            : Espen Carlsen
 *   based on             : phpbb3.functions.php
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

function pVerify($user) {
	global $db_raid, $pConfig;

	$userdata = array();

	// Make sure we don't have an anonymous user.
	if (isset($user) && $user->guest==0 && $user->id>0) {
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = 'profile_id='.intval($user->id);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	unset($sql);

	// if nothing returns we need to create profile
	// otherwise they have a profile so let's set their ID
	// we'll just use the Joomla user id as the profile ID to simplify things
	if($db_raid->sql_numrows() == 0) {
		// are they a super user?
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'profile_id>0';

		$check = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		unset($sql);

		// nothing returned, create profile
		$sql['INSERT'] = 'profile';
		$sql['VALUES'] = array(
							'profile_id'=>$user->id,
							'user_email'=>utf8_decode($user->email),
							'password'=>'',
							'group_id'=>(($db_raid->sql_numrows($check) == 0)?1:$pConfig['default_group']),
							'username'=>utf8_decode($user->username),
							'join_date'=>time());
		$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			$userdata['group_id'] = $sql['VALUES']['group_id'];

	} else {
		// profile exists set group
		$data = $db_raid->fetch();
			$userdata['group_id'] = $data['group_id'];
	}
		$userdata['profile_id'] = $user->id;
		$userdata['session_logged_in'] = 1;
		$userdata['username'] = utf8_decode($user->username);
		$userdata['user_email'] = utf8_decode($user->email);
		$userdata['timezone'] = $data['timezone'];
		$userdata['dst'] = $data['dst'];
	}
	return $userdata;
}

function pLogin() {
	global $pConfig,$pConfig_auth;

	// handle redirection because we're not using login_form
	pRedirect($pConfig_auth['register_url']);
	exit;
}

function pLogout() {
	// unset the session and remove all cookies
	global $mainframe, $dbf;

	$mainframe->logout();
	foreach($_SESSION as $key=>$value)
		unset($_SESSION[$key]);

	setcookie('username');
	setcookie('password');
}
session_destroy();

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define('JPATH_BASE', $pConfig_auth['joomla_path'] );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_LIBRARIES.DS.'loader.php');

//Base classes
JLoader::import( 'joomla.base.object' 			);
//Environment classes
JLoader::import( 'joomla.environment.request'   );

//Factory class and methods
JLoader::import( 'joomla.factory' 				);

//Error
JLoader::import( 'joomla.error.error' 			);
JLoader::import( 'joomla.error.exception' 		);

//Utilities
JLoader::import( 'joomla.utilities.arrayhelper' );

//Filters
JLoader::import( 'joomla.filter.filterinput'	);

//Register class that don't follow one file per class naming conventions
JLoader::register('JText' , dirname(__FILE__).DS.'methods.php');

//enr import.php
// Include object abstract class
jimport( 'joomla.utilities.compat.compat' );

// Joomla! library imports;
jimport( 'joomla.user.user');
jimport( 'joomla.environment.uri' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.event.event');
jimport( 'joomla.event.dispatcher');
//end framework.php

//global $mainframe;
$mainframe =& JFactory::getApplication('site');
$user = JFactory::getUser();

unset($option);
$pMain = new Mainframe(pVerify($user));
?>