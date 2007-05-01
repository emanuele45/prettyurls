<?php

/*******************************************************************************	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs - actions-ext-none v0.6

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

require_once($sourcedir . '/Subs-PrettyUrls.php');

//	Update the pretty_filters setting
$prettyFilters = unserialize($modSettings['pretty_filters']);
$prettyFilters['actions'] = array(
	'id' => 'actions',
	'filter' => array(
		'priority' => 90,
		'callback' => 'pretty_urls_actions_filter',
	),
	'rewrite' => array(
		'priority' => 20,
		'rule' => 'RewriteRule ^([a-zA-Z0-9]+)/?$ ./index.php?pretty;action=$1 [L,QSA]',
	),
);
updateSettings(array('pretty_filters' => addslashes(serialize($prettyFilters))));

//	Update everything now
updateFilters();

?>
