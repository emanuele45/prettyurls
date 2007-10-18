<?php
//	Version: 0.8; PrettyUrls-Filters
//	A file for filter extensions to be placed in

if (!defined('SMF'))
	die('Hacking attempt...');

//	Filter miscellaneous action urls
function pretty_urls_actions_filter($urls)
{
	global $scripturl, $boardurl;

	$pattern = '~(.*)action=([^;]+)~S';
	$replacement = $boardurl . '/$2/$1';
	foreach ($urls as $crc => $url)
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url']))
				$urls[$crc]['replacement'] = preg_replace($pattern, $replacement, $url['url']);
	return $urls;
}

//	Filter topic urls
function pretty_urls_topic_filter($urls)
{
	global $scripturl, $modSettings, $context, $db_prefix, $smfFunc;

	$pattern = '~(.*[?;&])topic=([.a-zA-Z0-9]+)(.*)~S';
	$query_data = array();
	foreach ($urls as $crc => $url)
	{
		//	Get the topic data ready to query the database with
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url'], $matches))
			{
				if (strpos($matches[2], '.') !== false)
					list ($urls[$crc]['topic_id'], $urls[$crc]['start']) = explode('.', $matches[2]);
				else
				{
					$urls[$crc]['topic_id'] = $matches[2];
					$urls[$crc]['start'] = '0';
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
		$query = $smfFunc['db_query']('', "
			SELECT t.id_topic, t.id_board, p.pretty_url
			FROM {$db_prefix}topics AS t
				LEFT JOIN {$db_prefix}pretty_topic_urls AS p ON (t.id_topic = p.id_topic)
			WHERE t.id_topic IN (" . implode(', ', array_keys(array_flip($query_data))) . ")", __FILE__, __LINE__);
		while ($row = $smfFunc['db_fetch_assoc']($query))
			$topicData[$row['id_topic']] = array(
				'pretty_board' => (isset($context['pretty']['board_urls'][$row['id_board']]) ? $context['pretty']['board_urls'][$row['id_board']] : $row['id_board']),
				'pretty_url' => isset($row['pretty_url']) ? $row['pretty_url'] : $row['id_topic'],
			);
		$smfFunc['db_free_result']($query);

		//	Build the replacement URLs
		foreach ($urls as $crc => $url)
			if (isset($url['topic_id']) && isset($topicData[$url['topic_id']]))
			{
				$start = $url['start'] != '0' || is_numeric($topicData[$url['topic_id']]['pretty_url']) ? $url['start'] . '/' : '';
				$urls[$crc]['replacement'] = $modSettings['pretty_root_url'] . '/' . $topicData[$url['topic_id']]['pretty_board'] . '/' . $topicData[$url['topic_id']]['pretty_url'] . '/' . $start . $url['match1'] . $url['match3'];
			}
	}
	return $urls;
}

//	Filter board urls
function pretty_urls_board_filter($urls)
{
	global $scripturl, $modSettings, $context;

	$pattern = '~(.*[?;&])board=([.0-9]+)(.*)~S';
	foreach ($urls as $crc => $url)
		//	Split out the board URLs and replace them
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url'], $matches))
			{
				if (strpos($matches[2], '.') !== false)
					list ($board_id, $start) = explode('.', $matches[2]);
				else
				{
					$board_id = $matches[2];
					$start = '0';
				}
				$board_id = (int) $board_id;
				$start = $start != '0' ? $start . '/' : '';
				$urls[$crc]['replacement'] = $modSettings['pretty_root_url'] . '/' . (isset($context['pretty']['board_urls'][$board_id]) ? $context['pretty']['board_urls'][$board_id] : $board_id) . '/' . $start . $matches[1] . $matches[3];
			}
	return $urls;
}

//	Filter profiles
function pretty_profiles_filter($urls)
{
	global $scripturl, $boardurl, $modSettings, $db_prefix, $smfFunc;

	$pattern = '~(.*)action=profile;u=([0-9]+)(.*)~S';
	$query_data = array();
	foreach ($urls as $crc => $url)
	{
		//	Get the profile data ready to query the database with
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url'], $matches))
			{
				$urls[$crc]['profile_id'] = (int) $matches[2];
				$urls[$crc]['match1'] = $matches[1];
				$urls[$crc]['match3'] = $matches[3];
				$query_data[] = $urls[$crc]['profile_id'];
			}
	}

	//	Query the database with these profile IDs
	if (count($query_data) != 0)
	{
		$memberNames = array();
		$query = $smfFunc['db_query']('', "
			SELECT id_member, member_name
			FROM {$db_prefix}members
			WHERE id_member IN (" . implode(', ', array_keys(array_flip($query_data))) . ")", __FILE__, __LINE__);
		while ($row = $smfFunc['db_fetch_assoc']($query))
			$memberNames[$row['id_member']] = rawurlencode($row['member_name']);
		$smfFunc['db_free_result']($query);

		//	Build the replacement URLs
		foreach ($urls as $crc => $url)
		{
			if (isset($url['profile_id']))
				$urls[$crc]['replacement'] = $boardurl . '/profile/' . $memberNames[$url['profile_id']] . '/' . $url['match1'] . $url['match3'];
		}
	}
	return $urls;
}

?>
