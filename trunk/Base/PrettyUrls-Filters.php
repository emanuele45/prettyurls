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
		20 => array('PrettyUrls-Filters.php', 'pretty_urls_board_filter'),
	);

	return array('filters' => $filterSettings);
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
