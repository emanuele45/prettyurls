<?php

/*******************************************************************************	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs Extras 0.9

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
}
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot uninstall - please verify you put this in the same place as SMF\'s SSI.php.');

require_once($sourcedir . '/Subs-PrettyUrls.php');

//	Remove these filters
$prettyFilters = unserialize($modSettings['pretty_filters']);
unset($prettyFilters['arcade']);
unset($prettyFilters['seo4smf']);
unset($prettyFilters['tp-articles']);

updateSettings(array('pretty_filters' => isset($smcFunc) ? serialize($prettyFilters) : addslashes(serialize($prettyFilters))));

//	Update everything now
pretty_update_filters();

?>
