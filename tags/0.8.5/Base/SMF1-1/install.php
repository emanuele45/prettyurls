<?php

/*******************************************************************************
	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs - Base v0.8.2

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
	$standalone = true;
}
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

//	Get the list of tasks ready
$tasks = array(
	'db' => 'Database modifications',
	'dbchanges' => array(),
);

//	Create the pretty_topic_urls table
db_query("
	CREATE TABLE IF NOT EXISTS {$db_prefix}pretty_topic_urls (
	`ID_TOPIC` mediumint(8) NOT NULL default '0',
	`pretty_url` varchar(80) NOT NULL,
	PRIMARY KEY (`ID_TOPIC`),
	UNIQUE (`pretty_url`))", __FILE__, __LINE__);
$tasks['dbchanges'][] = 'Creating the pretty_topic_urls table';

//	Fix old topics by replacing ' with chr(18)
db_query("
	UPDATE {$db_prefix}pretty_topic_urls
	SET pretty_url = REPLACE(pretty_url, '\\'', '" . chr(18) . "')", __FILE__, __LINE__);
$tasks['dbchanges'][] = 'Fixing any old topics with broken quotes';

//	Create the pretty_urls_cache table
db_query("DROP TABLE IF EXISTS {$db_prefix}pretty_urls_cache", __FILE__, __LINE__);
db_query("
	CREATE TABLE {$db_prefix}pretty_urls_cache (
	`url_id` VARCHAR(255) NOT NULL,
	`replacement` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`url_id`))", __FILE__, __LINE__);
$tasks['dbchanges'][] = 'Creating the pretty_urls_cache table';

//	Default filter settings
$prettyFilters = array(
	'boards' => array(
		'description' => 'Rewrite Board URLs',
		'enabled' => 1,
		'filter' => array(
			'priority' => 45,
			'callback' => 'pretty_urls_board_filter',
		),
		'rewrite' => array(
			'priority' => 40,
			'rule' => 'RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/?$ ./index.php?pretty;board=$1.0 [L,QSA]
RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/([0-9]*)/?$ ./index.php?pretty;board=$1.$2 [L,QSA]',
		),
		'title' => 'Boards',
	),
	'topics' => array(
		'description' => 'Rewrite Topic URLs',
		'enabled' => 1,
		'filter' => array(
			'priority' => 40,
			'callback' => 'pretty_urls_topic_filter',
		),
		'rewrite' => array(
			'priority' => 45,
			'rule' => 'RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/([-_!~*\'()$a-zA-Z0-9]+)/?$ ./index.php?pretty;board=$1;topic=$2.0 [L,QSA]
RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/([-_!~*\'()$a-zA-Z0-9]+)/([0-9]*|msg[0-9]*|new)/?$ ./index.php?pretty;board=$1;topic=$2.$3 [L,QSA]',
		),
		'title' => 'Topics',
	),
	'actions' => array(
		'description' => 'Rewrite Action URLs (ie, index.php?action=something)',
		'enabled' => 1,
		'filter' => array(
			'priority' => 90,
			'callback' => 'pretty_urls_actions_filter',
		),
		'rewrite' => array(
			'priority' => 20,
			'rule' => '#ACTIONS',	//	To be replaced in pretty_update_filters()
		),
		'title' => 'Actions',
	),
	'profiles' => array(
		'description' => 'Rewrite Profile URLs. As this uses the Username of an account rather than it\'s Display Name, it may not be desirable to your users.',
		'enabled' => 0,
		'filter' => array(
			'priority' => 80,
			'callback' => 'pretty_profiles_filter',
		),
		'rewrite' => array(
			'priority' => 15,
			'rule' => 'RewriteRule ^profile/([^/]+)/?$ ./index.php?pretty;action=profile;user=$1 [L,QSA]',
		),
		'title' => 'Profiles',
	),
);
$tasks[] = 'Adding the default filters';

//	Add the pretty_root_url and pretty_enable_filters settings:
$pretty_root_url = isset($modSettings['pretty_root_url']) ? $modSettings['pretty_root_url'] : $boardurl;
$pretty_enable_filters = isset($modSettings['pretty_enable_filters']) ? $modSettings['pretty_enable_filters'] : 0;

//	Update the settings table
updateSettings(array(
	'pretty_enable_filters' => $pretty_enable_filters,
	'pretty_filters' => addslashes(serialize($prettyFilters)),
	'pretty_root_url' => $pretty_root_url,
));
$tasks[] = 'Adding some settings';

//	Run maintenance
require_once($sourcedir . '/Subs-PrettyUrls.php');
pretty_run_maintenance();
$tasks[] = 'Running maintenance tasks';
$tasks[] = $context['pretty']['maintenance_tasks'];

//	Format the tasks list
$first = true;
$task_list = '<ul>';
foreach ($tasks as $task)
	if (is_array($task))
	{
		$task_list .= '<ul>';
		foreach ($task as $subtask)
			$task_list .= '<li>' . $subtask . '</li>';
		$task_list .= '</ul>';
	}
	else
	{
		if ($first = true)
			$first = false;
		else
			$task_list .= '</li>';
		$task_list .= '<li>' . $task;
	}
$task_list .= '</li></ul>';

//	Output the list of database changes
if (isset($standalone))
{
	echo '<title>Installing Pretty URLs - Base 0.8.2</title>
<h1>Installing Pretty URLs - Base 0.8.2</h1>
<h2>Database changes</h2>
', $task_list ;
}
else
	$txt['package_installed_done'] = $task_list . $txt['package_installed_done'];

?>
