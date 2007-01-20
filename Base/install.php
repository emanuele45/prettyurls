<?php

/*******************************************************************************	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs - Base v0.3

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

require_once($sourcedir . '/Subs-PrettyUrls.php');

//	Get the current pretty board urls, or make a new array if there are none
$pretty_board_urls = isset($modSettings['pretty_board_urls']) ? unserialize($modSettings['pretty_board_urls']) : array();

//	Get the board names
$query = db_query("
	SELECT ID_BOARD, name
	FROM {$db_prefix}boards", __FILE__, __LINE__);

while ($row = mysql_fetch_assoc($query))
{
//	Don't replace the board urls if they already exist
	if (!isset($pretty_board_urls[$row['ID_BOARD']]) || $pretty_board_urls[$row['ID_BOARD']] == '')
	{
		$pretty_text = generatePrettyUrl($row['name']);
//		Can't be empty, can't be a number and can't be the same as another
		if ($pretty_text != '' && !is_numeric($pretty_text) && !array_search($pretty_text, $pretty_board_urls))
			$pretty_board_urls[$row['ID_BOARD']] = $pretty_text;
		else
//			Add suffix '-bID_BOARD' to the pretty url
			$pretty_board_urls[$row['ID_BOARD']] = $pretty_text . ($pretty_text != '' ? '-b' : 'b') . $row['ID_BOARD'];
	}
}
mysql_free_result($query);

//	Create the pretty_topic_urls table
db_query("
	CREATE TABLE IF NOT EXISTS {$db_prefix}pretty_topic_urls (
	`ID_TOPIC` mediumint(8) NOT NULL default '0',
	`ID_BOARD` smallint(5) NOT NULL default '0',
	`pretty_url` varchar(80) NOT NULL,
	PRIMARY KEY (`ID_TOPIC`),
	UNIQUE (`pretty_url`))", __FILE__, __LINE__);

//	Add a pretty_url column to the topics table
$query = db_query("
	SHOW COLUMNS
	FROM {$db_prefix}topics
	LIKE 'pretty_url'", __FILE__, __LINE__);
$no_upgrade = mysql_num_rows($query) > 0;
if (!$no_upgrade)
	db_query("
		ALTER TABLE {$db_prefix}topics
		ADD `pretty_url` varchar(80) NOT NULL", __FILE__, __LINE__);
mysql_free_result($query);

//	Synchronise the topic URLs
synchroniseTopicUrls();

//	Add the pretty_root_url setting to the settings table:
db_query("
	INSERT IGNORE INTO {$db_prefix}settings (variable, value)
	VALUES ('pretty_root_url', '" . $boardurl . "')", __FILE__, __LINE__);

//	Update the settings table
updateSettings(array('pretty_board_urls' => serialize($pretty_board_urls)));

//	Add the Package List if it hasn't been added already
	$query = db_query("
		SELECT url
		FROM {$db_prefix}package_servers
		WHERE url = 'http://prettyurls.googlecode.com/svn/trunk'
		LIMIT 1", __FILE__, __LINE__);
	if (mysql_num_rows($query) == 0)
	{
		db_query("
			INSERT INTO {$db_prefix}package_servers (name, url)
			VALUES ('Pretty URLs Package List', 'http://prettyurls.googlecode.com/svn/trunk')", __FILE__, __LINE__);
	}
	mysql_free_result($query);

?>
