<?php

/*******************************************************************************	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs mod v0.1

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

require_once($sourcedir . '/Subs-PrettyUrls.php');

//	Add the pretty_root_url setting to the settings table:
db_query("
	INSERT IGNORE INTO {$db_prefix}settings (variable, value)
	VALUES ('pretty_root_url', '" . $boardurl . "')", __FILE__, __LINE__);

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
	}
}

//	Update the settings table
updateSettings(array('pretty_board_urls' => serialize($pretty_board_urls)));

?>
