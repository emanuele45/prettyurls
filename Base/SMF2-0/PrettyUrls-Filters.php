<?php
//	Version: 0.9; PrettyUrls-Filters
//	A file for filter extensions to be placed in

if (!defined('SMF'))
	die('Hacking attempt...');

//	Filter miscellaneous action urls
function pretty_urls_actions_filter($urls)
{
	global $boardurl, $context, $scripturl;

	$pattern = '~' . $scripturl . '(.*)action=([^;]+)~S';
	$replacement = $boardurl . '/$2/$1';
	foreach ($urls as $url_id => $url)
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url'], $matches))
				if (in_array($matches[2], $context['pretty']['action_array']))
					$urls[$url_id]['replacement'] = preg_replace($pattern, $replacement, $url['url']);
	return $urls;
}

//	Filter topic urls
function pretty_urls_topic_filter($urls)
{
	global $context, $modSettings, $scripturl, $smcFunc, $sourcedir;

	$pattern = '~' . $scripturl . '(.*[?;&])topic=([.a-zA-Z0-9]+)(.*)~S';
	$query_data = array();
	foreach ($urls as $url_id => $url)
	{
		//	Get the topic data ready to query the database with
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url'], $matches))
			{
				if (strpos($matches[2], '.') !== false)
					list ($urls[$url_id]['topic_id'], $urls[$url_id]['start']) = explode('.', $matches[2]);
				else
				{
					$urls[$url_id]['topic_id'] = $matches[2];
					$urls[$url_id]['start'] = '0';
				}
				$urls[$url_id]['topic_id'] = (int) $urls[$url_id]['topic_id'];
				$urls[$url_id]['match1'] = $matches[1];
				$urls[$url_id]['match3'] = $matches[3];
				$query_data[] = $urls[$url_id]['topic_id'];
			}
	}

	//	Query the database with these topic IDs
	if (count($query_data) != 0)
	{
		//	Look for existing topic URLs
		$query_data = array_keys(array_flip($query_data));
		$topicData = array();
		$unpretty_topics = array();

		$query = $smcFunc['db_query']('', '
			SELECT t.id_topic, t.id_board, p.pretty_url
			FROM {db_prefix}topics AS t
				LEFT JOIN {db_prefix}pretty_topic_urls AS p ON (t.id_topic = p.id_topic)
			WHERE t.id_topic IN ({array_int:topic_ids})',
			array('topic_ids' => $query_data));

		while ($row = $smcFunc['db_fetch_assoc']($query))
			if (isset($row['pretty_url']))
				$topicData[$row['id_topic']] = array(
					'pretty_board' => (isset($context['pretty']['board_urls'][$row['id_board']]) ? $context['pretty']['board_urls'][$row['id_board']] : $row['id_board']),
					'pretty_url' => $row['pretty_url'],
				);
			else
				$unpretty_topics[] = $row['id_topic'];
		$smcFunc['db_free_result']($query);

		//	Generate new topic URLs if required
		if (count($unpretty_topics) != 0)
		{
			require_once($sourcedir . '/Subs-PrettyUrls.php');

			//	Get the topic subjects
			$new_topics = array();
			$new_urls = array();
			$query_check = array();
			$existing_urls = array();
			$add_new = array();

			$query = $smcFunc['db_query']('', '
				SELECT t.id_topic, t.id_board, m.subject
				FROM {db_prefix}topics AS t
					INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
				WHERE t.id_topic IN ({array_int:topic_ids})',
				array('topic_ids' => $unpretty_topics));

			while ($row = $smcFunc['db_fetch_assoc']($query))
				$new_topics[] = array(
					'id_topic' => $row['id_topic'],
					'id_board' => $row['id_board'],
					'subject' => $row['subject'],
				);
			$smcFunc['db_free_result']($query);

			//	Generate URLs for each new topic
			foreach ($new_topics as $row)
			{
				$pretty_text = substr(pretty_generate_url($row['subject']), 0, 80);
				//	A topic in the recycle board doesn't deserve a proper URL
				if (($modSettings['recycle_enable'] && $row['id_board'] == $modSettings['recycle_board']) || $pretty_text == '')
					//	Use 'tID_TOPIC' as a pretty url
					$pretty_text = 't' . $row['id_topic'];
				//	No duplicates and no numerical URLs - that would just confuse everyone!
				if (in_array($pretty_text, $new_urls) || is_numeric($pretty_text))
					//	Add suffix '-tID_TOPIC' to the pretty url
					$pretty_text = substr($pretty_text, 0, 70) . '-t' . $row['id_topic'];
				$query_check[] = $pretty_text;
				$new_urls[$row['id_topic']] = $pretty_text;
			}

			//	Find any duplicates of existing URLs
			$query = $smcFunc['db_query']('', '
				SELECT pretty_url
				FROM {db_prefix}pretty_topic_urls
				WHERE pretty_url IN ({array_string:new_urls})',
				array('new_urls' => $query_check));
			while ($row = $smcFunc['db_fetch_assoc']($query))
				$existing_urls[] = $row['pretty_url'];
			$smcFunc['db_free_result']($query);

			//	Finalise the new URLs ...
			foreach ($new_topics as $row)
			{
				$pretty_text = $new_urls[$row['id_topic']];
				//	Check if the new URL is already in use
				if (in_array($pretty_text, $existing_urls))
					$pretty_text = substr($pretty_text, 0, 70) . '-t' . $row['id_topic'];
				$add_new[] = array($row['id_topic'], $pretty_text);
				//	Add to the original array of topic URLs
				$topicData[$row['id_topic']] = array(
					'pretty_board' => (isset($context['pretty']['board_urls'][$row['id_board']]) ? $context['pretty']['board_urls'][$row['id_board']] : $row['id_board']),
					'pretty_url' => $pretty_text,
				);
			}
			//	... and add them to the database!
			$smcFunc['db_insert']('',
				'{db_prefix}pretty_topic_urls',
				array('id_topic' => 'int', 'pretty_url' => 'string'),
				$add_new,
				array());
		}

		//	Build the replacement URLs
		foreach ($urls as $url_id => $url)
			if (isset($url['topic_id']) && isset($topicData[$url['topic_id']]))
			{
				$start = $url['start'] != '0' || is_numeric($topicData[$url['topic_id']]['pretty_url']) ? $url['start'] . '/' : '';
				$urls[$url_id]['replacement'] = $modSettings['pretty_root_url'] . '/' . $topicData[$url['topic_id']]['pretty_board'] . '/' . $topicData[$url['topic_id']]['pretty_url'] . '/' . $start . $url['match1'] . $url['match3'];
			}
	}
	return $urls;
}

//	Filter board urls
function pretty_urls_board_filter($urls)
{
	global $scripturl, $modSettings, $context;

	$pattern = '~' . $scripturl . '(.*[?;&])board=([.0-9]+)(.*)~S';
	foreach ($urls as $url_id => $url)
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
				$urls[$url_id]['replacement'] = $modSettings['pretty_root_url'] . '/' . (isset($context['pretty']['board_urls'][$board_id]) ? $context['pretty']['board_urls'][$board_id] : $board_id) . '/' . $start . $matches[1] . $matches[3];
			}
	return $urls;
}

//	Filter profiles
function pretty_profiles_filter($urls)
{
	global $boardurl, $modSettings, $scripturl, $smcFunc;

	$pattern = '~' . $scripturl . '(.*)action=profile;u=([0-9]+)(.*)~S';
	$query_data = array();
	foreach ($urls as $url_id => $url)
	{
		//	Get the profile data ready to query the database with
		if (!isset($url['replacement']))
			if (preg_match($pattern, $url['url'], $matches))
			{
				$urls[$url_id]['profile_id'] = (int) $matches[2];
				$urls[$url_id]['match1'] = $matches[1];
				$urls[$url_id]['match3'] = $matches[3];
				$query_data[] = $urls[$url_id]['profile_id'];
			}
	}

	//	Query the database with these profile IDs
	if (count($query_data) != 0)
	{
		$query = $smcFunc['db_query']('', '
			SELECT id_member, member_name
			FROM {db_prefix}members
			WHERE id_member IN ({array_int:member_ids})',
			array('member_ids' => $query_data));

		$memberNames = array();
		while ($row = $smcFunc['db_fetch_assoc']($query))
			$memberNames[$row['id_member']] = rawurlencode($row['member_name']);
		$smcFunc['db_free_result']($query);

		//	Build the replacement URLs
		foreach ($urls as $url_id => $url)
		{
			if (isset($url['profile_id']))
				$urls[$url_id]['replacement'] = $boardurl . '/profile/' . $memberNames[$url['profile_id']] . '/' . $url['match1'] . $url['match3'];
		}
	}
	return $urls;
}

?>
