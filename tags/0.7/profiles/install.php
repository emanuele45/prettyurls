<?php

/*******************************************************************************
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs - profiles-ext-none v0.7

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

require_once($sourcedir . '/Subs-PrettyUrls.php');

//	Update the pretty_filters setting
$prettyFilters = unserialize($modSettings['pretty_filters']);
$prettyFilters['profiles'] = array(
	'id' => 'profiles',
	'filter' => array(
		'priority' => 80,
		'callback' => 'pretty_profiles_filter',
	),
	'rewrite' => array(
		'priority' => 15,
		'rule' => 'RewriteRule ^profile/([^/]+)/?$ ./index.php?pretty;action=profile;user=$1 [L,QSA]',
	),
);
updateSettings(array('pretty_filters' => addslashes(serialize($prettyFilters))));

//	Update everything now
updateFilters();

?>