<?php

/*******************************************************************************	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs - Base v0.8

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

//	Get the current pretty board urls, or make new arrays if there are none
$pretty_board_urls = isset($modSettings['pretty_board_urls']) ? unserialize($modSettings['pretty_board_urls']) : array();
$pretty_board_lookup = isset($modSettings['pretty_board_lookup']) ? unserialize($modSettings['pretty_board_lookup']) : array();

//	Get the board names
$query = db_query("
	SELECT ID_BOARD, name
	FROM {$db_prefix}boards", __FILE__, __LINE__);

while ($row = mysql_fetch_assoc($query))
{
//	Don't replace the board urls if they already exist
	if (!isset($pretty_board_urls[$row['ID_BOARD']]) || $pretty_board_urls[$row['ID_BOARD']] == '' || array_search($row['ID_BOARD'], $pretty_board_lookup) === false)
	{
		$pretty_text = pretty_generate_url($row['name']);
		//	Can't be empty, can't be a number and can't be the same as another
		if ($pretty_text == '' || is_numeric($pretty_text) || isset($pretty_board_lookup[$pretty_text]))
			//	Add suffix '-bID_BOARD' to the pretty url
			$pretty_text .= ($pretty_text != '' ? '-b' : 'b') . $row['ID_BOARD'];
		//	Update the arrays
		$pretty_board_urls[$row['ID_BOARD']] = $pretty_text;
		$pretty_board_lookup[$pretty_text] = $row['ID_BOARD'];
	}
}
mysql_free_result($query);
$output .= '<li>Generating board URLs</li>';

//	Create the pretty_topic_urls table
db_query("
	CREATE TABLE IF NOT EXISTS {$db_prefix}pretty_topic_urls (
	`ID_TOPIC` mediumint(8) NOT NULL default '0',
	`pretty_url` varchar(80) NOT NULL,
	PRIMARY KEY (`ID_TOPIC`),
	UNIQUE (`pretty_url`))", __FILE__, __LINE__);
$output .= '<li>Creating the pretty_topic_urls table</li>';

//	Create the pretty_urls_cache table
db_query("
	CREATE TABLE IF NOT EXISTS {$db_prefix}pretty_urls_cache (
	`url_crc` INT NOT NULL default '0',
	`replacement` TEXT NOT NULL,
	`log_time` TIMESTAMP NOT NULL,
	PRIMARY KEY (`url_crc`))", __FILE__, __LINE__);
$output .= '<li>Creating the pretty_urls_cache table</li>';

//	Build the table of topic URLs
pretty_synchronise_topic_urls();
$output .= '<li>Building the list of topic URLs</li>';

//	Add the pretty_root_url and pretty_enable_filters settings:
db_query("
	INSERT IGNORE INTO {$db_prefix}settings (variable, value)
	VALUES ('pretty_root_url', '$boardurl'),
		('pretty_enable_filters', '0')", __FILE__, __LINE__);

//	Default filter settings
$prettyFilters = array(
	'topics' => array(
		'id' => 'topics',
		'filter' => array(
			'priority' => 40,
			'callback' => 'pretty_urls_topic_filter',
		),
		'rewrite' => array(
			'priority' => 45,
			'rule' => 'RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/([-_!~*\'()$a-zA-Z0-9]+)/?$ ./index.php?pretty;board=$1;topic=$2.0 [L,QSA]
RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/([-_!~*\'()$a-zA-Z0-9]+)/([0-9]*|msg[0-9]*|new)/?$ ./index.php?pretty;board=$1;topic=$2.$3 [L,QSA]',
		),
	),
	'boards' => array(
		'id' => 'boards',
		'filter' => array(
			'priority' => 45,
			'callback' => 'pretty_urls_board_filter',
		),
		'rewrite' => array(
			'priority' => 40,
			'rule' => 'RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/?$ ./index.php?pretty;board=$1.0 [L,QSA]
RewriteRule ^ROOTURL([-_!~*\'()$a-zA-Z0-9]+)/([0-9]*)/?$ ./index.php?pretty;board=$1.$2 [L,QSA]',
		),
	),
);

//	Update the settings table
updateSettings(array(
	'pretty_board_lookup' => addslashes(serialize($pretty_board_lookup)),
	'pretty_board_urls' => addslashes(serialize($pretty_board_urls)),
	'pretty_filters' => addslashes(serialize($prettyFilters)),
));
$output .= '<li>Adding some settings</li>';

//	Update the filter callbacks
pretty_update_filters();
$output .= '<li>Processing the installed filters</li>';

//	Add the Package List if it hasn't been added already
$query = db_query("
	SELECT url
	FROM {$db_prefix}package_servers
	WHERE url = 'http://prettyurls.googlecode.com/svn/trunk'
	LIMIT 1", __FILE__, __LINE__);
if (mysql_num_rows($query) == 0)
	db_query("
		INSERT INTO {$db_prefix}package_servers (name, url)
		VALUES ('Pretty URLs Package List', 'http://prettyurls.googlecode.com/svn/trunk')", __FILE__, __LINE__);
mysql_free_result($query);
$output .= '<li>Adding a package server</li></ul>';

//	Output the list of database changes
$txt['package_installed_done'] = $output . $txt['package_installed_done'];
if (isset($standalone))
{
	echo '<title>Installing Pretty URLs - Base 0.7</title>
<h1>Installing Pretty URLs - Base 0.7</h1>
<h2>Database changes</h2>
', $txt['package_installed_done'];
}

?>
