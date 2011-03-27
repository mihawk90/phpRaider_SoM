<?php
class Mainframe {
	var $permissions = array(); // user permissions
	var $characters = array(); // holds character information
	var $roles = array(); // holds role information
	var $username; // holds the users username
	var $email; // holds user email
	var $profile_id; // holds the profile_id of the user
	var $group_id; // group id of user
	var $logged_in; // 1 if logged in, else 0
	var $_default = array(
		'dst'=>0,
		'email'=>'anonymous@anonymous.com',
		'group_id'=>0,
		'logged_in'=>0,
		'profile_id'=>-1,
		'timezone'=>0,
		'username'=>'anonymous');

	// constructor
	function Mainframe($data = null) {
		$this->permissions = array();
		$this->characters = array();
		$this->roles = array();
		// set variables
		if (isset($data) && is_array($data)) {
			$this->logged_in = ((isset($data['session_logged_in']))?$data['session_logged_in']:$this->_default['logged_in']);
			$this->profile_id = ((isset($data['profile_id']))?$data['profile_id']:$this->_default['profile_id']);
			$this->group_id = ((isset($data['group_id']))?$data['group_id']:$this->_default['group_id']);
			$this->username = ((isset($data['username']))?$data['username']:$this->_default['username']);
			$this->email = ((isset($data['user_email']))?$data['user_email']:$this->_default['email']);
			$this->timezone = ((isset($data['timezone']))?$data['timezone']:$this->_default['timezone']);
			$this->dst = ((isset($data['dst']))?$data['dst']:$this->_default['dst']);
		} else {
			$this->logged_in=$this->_default['logged_in'];
			$this->profile_id=$this->_default['profile_id'];
			$this->group_id=$this->_default['group_id'];
			$this->username=$this->_default['username'];
			$this->email=$this->_default['email'];
			$this->timezone=$this->_default['timezone'];
			$this->dst=$this->_default['dst'];
		}
	}

	// verifies user has permissions
	function checkPerm($name) {
		if(!empty($this->permissions[$name])) { return true; } else { return false; };
	}

	function getPerm() {
		return $this->permissions;
	}

	// sets permissions for user
	function setPerm($name){
		$this->permissions[$name] = 1;
	}

	// returns username
	function getUser() {
		return $this->username;
	}

	// returns users email
	function getEmail() {
		return $this->email;
	}

	function getTimezone() {
		return $this->timezone;
	}

	function getDST() {
		return $this->dst;
	}

	// returns users profile ID
	function getProfileID() {
		return $this->profile_id;
	}

	// returns users group ID
	function getGroupID() {
		return $this->group_id;
	}

	// verifies session has been initated
	function getInitiated() {
		return $this->initiated;
	}

	// returns if user is logged in
	function getLogged() {
		return $this->logged_in;
	}
}
?>