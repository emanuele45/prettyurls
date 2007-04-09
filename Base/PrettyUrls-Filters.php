<?php
//	Version: 0.6; PrettyUrls-Filters
//	A file for filter extensions to be placed in

if (!defined('SMF'))
	die('Hacking attempt...');

//	Filter and .htaccess settings - editable by mods
function filterAndHtaccessSettings()
{
	//	Filter callback settings
	$filterSettings = array(
		10 => array('PrettyUrls-Filters.php', 'pretty_urls_topic_filter'),
		20 => array('PrettyUrls-Filters.php', 'pretty_urls_board_filter'),
	);

	return array('filters' => $filterSettings);
}

//	Filter topic urls
function pretty_urls_topic_filter($urls)
{
	global $scripturl, $modSettings, $context, $db_prefix;

	$pattern = '~' . $scripturl . '(.*)topic=([.a-zA-Z0-9]+)(.*)~S';
	$query_data = array();
	foreach ($urls as $crc => $url)
	{
		//	Get the topic data ready to query the database with
		$found = preg_match($pattern, $url['url'], $matches);
		if (!isset($url['replacement']) && $found)
		{
			if (strpos($matches[2], '.') !== false)
				list ($urls[$crc]['topic_id'], $urls[$crc]['start']) = explode('.', $matches[2]);
			else
			{
				$urls[$crc]['topic_id'] = $matches[2];
				$urls[$crc]['start'] = 0;
			}
			$urls[$crc]['topic_id'] = (int) $urls[$crc]['topic_id'];
			$urls[$crc]['match1'] = $matches[1];
			$urls[$crc]['match3'] = $matches[3];
			$query_data[] = $urls[$crc]['topic_id'];
		}
	}

	//	Query the database with these topic IDs
	if (count($query_data) != 0)
	{
		$topicData = array();
		$query = db_query("
			SELECT ID_TOPIC, pretty_url, ID_BOARD
			FROM {$db_prefix}pretty_topic_urls
			WHERE ID_TOPIC IN (" . implode(', ', $query_data) . ")", __FILE__, __LINE__);
		while ($row = mysql_fetch_assoc($query))
			$topicData[$row['ID_TOPIC']] = array(
				'pretty_board' => (isset($context['pretty']['board_urls'][$row['ID_BOARD']]) ? $context['pretty']['board_urls'][$row['ID_BOARD']] : $row['ID_BOARD']),
				'pretty_url' => $row['pretty_url'],
			);
		mysql_free_result($query);
	}

	//	Build the replacement URLs
	foreach ($urls as $crc => $url)
	{
		if (isset($url['topic_id']))
			$urls[$crc]['replacement'] = $modSettings['pretty_root_url'] . '/' . $topicData[$url['topic_id']]['pretty_board'] . '/' . $topicData[$url['topic_id']]['pretty_url'] . '/' . $url['start'] . '/' . $url['match1'] . $url['match3'];
	}
	return $urls;
}

//	Filter board urls
function pretty_urls_board_filter($urls)
{
	global $scripturl, $modSettings, $context;

	$pattern = '~' . $scripturl . '(.*)board=([.0-9]+)(.*)~S';
	foreach ($urls as $crc => $url)
	{
		$found = preg_match($pattern, $url['url'], $matches);
		if (!isset($url['replacement']) && $found)
		{
			if (strpos($matches[2], '.') !== false)
				list ($board_id, $start) = explode('.', $matches[2]);
			else
			{
				$board_id = $matches[2];
				$start = 0;
			}
			$board_id = (int) $board_id;
			$urls[$crc]['replacement'] = $modSettings['pretty_root_url'] . '/' . (isset($context['pretty']['board_urls'][$board_id]) ? $context['pretty']['board_urls'][$board_id] : $board_id) . '/' . $start . '/' . $matches[1] . $matches[3];	
		}
	}
	return $urls;
}

?>
