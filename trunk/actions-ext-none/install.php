<?php

/*******************************************************************************	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs - actions-ext-none v0.8

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
	$standalone = true;
	$txt = array('package_installed_done' => '');
}
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

//	Start the list
$output = '<ul>';

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
$output .= '<li>Adding the actions filter</li>';

//	Update everything now
updateFilters();
$output .= '<li>Processing the installed filters</li></ul>';

//	Output the list of database changes
$txt['package_installed_done'] = $output . $txt['package_installed_done'];
if (isset($standalone))
{
	echo '<title>Installing Pretty URLs - Actions 0.7</title>
<h1>Installing Pretty URLs - Actions 0.7</h1>
<h2>Database changes</h2>
', $txt['package_installed_done'];
}

?>
