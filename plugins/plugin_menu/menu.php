<?php
// attempt to load the menu from the session
//if(($menu = (SmartyMenu::loadMenu('mymenu'))) === false) {
	// ROSTER SUBMENU
    SmartyMenu::initMenu($roster_sub);

	if($pMain->checkPerm('view_roster')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mrCharacters']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_roster');
		SmartyMenu::addMenuItem($roster_sub, $item);
	}
	
	if($pMain->checkPerm('view_members')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mrMembers']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_members');
		SmartyMenu::addMenuItem($roster_sub, $item);
	}
	
	// PROFILE SUBMENU
    SmartyMenu::initMenu($profile_sub);

	if($pMain->getLogged()) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpUpdate']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_profile');
		SmartyMenu::addMenuItem($profile_sub, $item);
	}
	
	if($pMain->checkPerm('edit_announcements_own')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpAnnouncements']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_announcements');
		SmartyMenu::addMenuItem($profile_sub, $item);
	}
	
	if($pMain->checkPerm('edit_characters_own')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpCharacters']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_characters');
		SmartyMenu::addMenuItem($profile_sub, $item);
	}
	
	if($pMain->checkPerm('view_history_own')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpHistory']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_history');
		SmartyMenu::addMenuItem($profile_sub, $item);
	}
	
	if($pMain->checkPerm('edit_raids_own')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpRaids']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_raids');
		SmartyMenu::addMenuItem($profile_sub, $item);
	}
	
	/*
	if($pMain->checkPerm('edit_subscriptions_own')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpSubscriptions']);
		SmartyMenu::setItemLink($item, '');
		SmartyMenu::addMenuItem($profile_sub, $item);
	}*/
	
	// ADMINISTER SUBMENU
    SmartyMenu::initMenu($administer);
	
	if($pMain->checkPerm('allow_backups')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maBackups']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_backups');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_configuration')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maConfiguration']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_configuration');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_announcements_any')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpAnnouncements']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_announcements');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_attributes')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maAttributes']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_attributes');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_definitions')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maDefinitions']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_definitions');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_genders')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maGenders']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_genders');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_groups')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maGroups']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_groups');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_guilds')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maGuilds']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_guilds');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_permissions')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maPermissions']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_permissions');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_raids_any')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mpRaids']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_raids');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_roles')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maRoles']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_roles');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	if($pMain->checkPerm('edit_raid_templates')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['maRaid_templates']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_templates');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
		if($pMain->checkPerm('edit_raids_any')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['luaout']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_lua_output');
		SmartyMenu::addMenuItem($administer_sub, $item);
	}
	
	// CREATE SUBMENU
    SmartyMenu::initMenu($create_sub);

	if($pMain->checkPerm('edit_announcements_own') || $pMain->checkPerm('edit_announcements_any')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mcAnnouncement']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_announcements&task=new');
		SmartyMenu::addMenuItem($create_sub, $item);
	}
	
	if($pMain->checkPerm('edit_characters_own') || $pMain->checkPerm('edit_characters_any')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mcCharacter']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_characters&task=new');
		SmartyMenu::addMenuItem($create_sub, $item);
	}
	
	if($pMain->checkPerm('edit_raids_own') || $pMain->checkPerm('edit_raids_any')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, $pLang['mcRaid']);
		SmartyMenu::setItemLink($item, 'index.php?option=com_raids&task=new');
		SmartyMenu::addMenuItem($create_sub, $item);
	}
	
    // Now we create the top-level menu
    SmartyMenu::initMenu($menu);

    // create and add items
    SmartyMenu::initItem($item);
    SmartyMenu::setItemText($item, '<img src="templates/'.$pConfig['template'].'/images/menu_home.png" border="0"> '.$pLang['mHome']);
    SmartyMenu::setItemLink($item, 'index.php');
    SmartyMenu::addMenuItem($menu, $item);

	if($pMain->checkPerm('view_roster') || $pMain->checkPerm('view_members')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, '<img src="templates/'.$pConfig['template'].'/images/menu_roster.png" border="0"> '.$pLang['mRoster']);
		SmartyMenu::setItemLink($item, '');
		SmartyMenu::setItemSubmenu($item, $roster_sub);
		SmartyMenu::addMenuItem($menu, $item);
	}
	
	if($pMain->checkPerm('view_history_own') || $pMain->checkPerm('edit_announcements_own') || 
	$pMain->checkPerm('edit_characters_own') || $pMain->checkPerm('edit_subscriptions_own') || 
	$pMain->checkPerm('edit_raids_own')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, '<img src="templates/'.$pConfig['template'].'/images/menu_profile.png" border="0"> '.$pLang['mProfile']);
		SmartyMenu::setItemLink($item, '');
		SmartyMenu::setItemSubmenu($item, $profile_sub);
		SmartyMenu::addMenuItem($menu, $item);
	}
	
	if($pMain->checkPerm('edit_configuration') || $pMain->checkPerm('edit_attributes') ||
	$pMain->checkPerm('edit_genders') || $pMain->checkPerm('edit_guilds') ||
	$pMain->checkPerm('edit_permissions') || $pMain->checkPerm('edit_races') || 
	$pMain->checkPerm('edit_raid_templates')) {
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, '<img src="templates/'.$pConfig['template'].'/images/menu_administer.png" border="0"> '.$pLang['mAdmin']);
		SmartyMenu::setItemLink($item, '');
		SmartyMenu::setItemSubmenu($item, $administer_sub);
		SmartyMenu::addMenuItem($menu, $item);
	}
	
	if($pMain->checkPerm('edit_characters_any') || $pMain->checkPerm('edit_characters_own') ||
	$pMain->checkPerm('edit_announcements_any') || $pMain->checkPerm('edit_announcements_own') ||
	$pMain->checkPerm('edit_raids_any') || $pMain->checkPerm('edit_raids_own')) {	
		SmartyMenu::initItem($item);
		SmartyMenu::setItemText($item, '<img src="templates/'.$pConfig['template'].'/images/menu_create.png" border="0"> '.$pLang['mCreate']);
		SmartyMenu::setItemLink($item, '');
		SmartyMenu::setItemSubmenu($item, $create_sub);
		SmartyMenu::addMenuItem($menu, $item);
	}
	  
	// save the menu into the session
	//SmartyMenu::saveMenu('mymenu', $menu);   
//}
    
$p->assign('menu', $menu);
?>