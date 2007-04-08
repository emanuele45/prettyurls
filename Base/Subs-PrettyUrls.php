<?php
//	Version: 0.6; Subs-PrettyUrls

if (!defined('SMF'))
	die('Hacking attempt...');

//	Generate a pretty URL from a given text
function generatePrettyUrl($text)
{
	global $modSettings;

	$characterHash = array (
		'a'	=>	array ('a', 'A', 'à', 'À', 'á', 'Á', 'â', 'Â', 'ã', 'Ã', 'ä', 'Ä', 'å', 'Å', 'ª', 'ą', 'Ą', 'а', 'А'),
		'aa'	=>	array ('ا'),
		'ae'	=>	array ('æ', 'Æ', 'ﻯ'),
		'and'	=>	array ('&'),
		'at'	=>	array ('@'),
		'b'	=>	array ('b', 'B', 'б', 'Б', 'ب'),
		'c'	=>	array ('c', 'C', 'ç', 'Ç', 'ć', 'Ć'),
		'cent'	=>	array ('¢'),
		'ch'	=>	array ('ч', 'Ч'),
		'copyright'	=>	array ('©'),
		'd'	=>	array ('d', 'D', 'Ð', 'д', 'Д', 'د', 'ض'),
		'degrees'	=>	array ('°'),
		'dh'	=>	array('ذ'),
		'dollar'	=>	array ('$'),
		'e'	=>	array ('e', 'E', 'è', 'È', 'é', 'É', 'ê', 'Ê', 'ë', 'Ë', 'ę', 'Ę', 'е', 'Е', 'ё', 'Ё', 'э', 'Э'),
		'f'	=>	array ('f', 'F', 'ф', 'Ф', 'ﻑ'),
		'g'	=>	array ('g', 'G', 'ğ', 'Ğ', 'г', 'Г'),
		'gh'	=>	array ('غ'),
		'h'	=>	array ('h', 'H', 'ح', 'ه'),
		'half'	=>	array ('½'),
		'i'	=>	array ('i', 'I', 'ì', 'Ì', 'í', 'Í', 'î', 'Î', 'ï', 'Ï', 'ı', 'İ', 'и', 'И'),
		'j'	=>	array ('j', 'J', 'ج'),
		'k'	=>	array ('k', 'K', 'к', 'К', 'ك'),
		'kh'	=>	array ('х', 'Х', 'خ'),
		'l'	=>	array ('l', 'L', 'ł', 'Ł', 'л', 'Л', 'ل'),
		'la'	=>	array ('ﻻ'),
		'm'	=>	array ('m', 'M', 'м', 'М', 'م'),
		'n'	=>	array ('n', 'N', 'ñ', 'Ñ', 'ń', 'Ń', 'н', 'Н', 'ن'),
		'o'	=>	array ('o', 'O', 'ò', 'Ò', 'ó', 'Ó', 'ô', 'Ô', 'õ', 'Õ', 'ö', 'Ö', 'ø', 'Ø', 'º', 'о', 'О'),
		'p'	=>	array ('p', 'P', 'п', 'П'),
		'percent'	=>	array ('%'),
		'plus'	=>	array ('+'),
		'plusminus'	=>	array ('±'),
		'pound'	=>	array ('£'),
		'q'	=>	array ('q', 'Q', 'ق'),
		'quarter'	=>	array ('¼'),
		'r'	=>	array ('r', 'R', '®', 'р', 'Р', 'ر'),
		's'	=>	array ('s', 'S', 'ş', 'Ş', 'ś', 'Ś', 'с', 'С', 'س', 'ص'),
		'section'	=>	array ('§'),
		'sh'	=>	array ('ш', 'Ш', 'ش'),
		'shch'	=>	array ('щ', 'Щ'),
		'ss'	=>	array ('ß'),
		't'	=>	array ('t', 'T', 'т', 'Т', 'ت', 'ط'),
		'th'	=>	array ('ث'),
		'three-quarters'	=>	array ('¾'),
		'ts'	=>	array ('ц', 'Ц'),
		'u'	=>	array ('u', 'U', 'ù', 'Ù', 'ú', 'Ú', 'û', 'Û', 'ü', 'Ü', 'µ', 'у', 'У'),
		'v'	=>	array ('v', 'V', 'в', 'В'),
		'w'	=>	array ('w', 'W', 'و'),
		'x'	=>	array ('x', 'X', '×'),
		'y'	=>	array ('y', 'Y', 'ý', 'Ý', 'ÿ', 'й', 'Й', 'ы', 'Ы', 'ي'),
		'ya'	=>	array ('я', 'Я'),
		'yen'	=>	array ('¥'),
		'yu'	=>	array ('ю', 'Ю'),
		'z'	=>	array ('z', 'Z', 'ż', 'Ż', 'ź', 'Ź', 'з', 'З', 'ز', 'ظ'),
		'zh'	=>	array ('ж', 'Ж'),
		'-'	=>	array ('-', ' ', '.', ','),
		'_'	=>	array ('_'),
		'!'	=>	array ('!'),
		'~'	=>	array ('~'),
		'*'	=>	array ('*'),
		"\\'"	=>	array ("'", '"', 'ﺀ', 'ع'),
		'('	=>	array ('('),
		')'	=>	array (')'),
		'0'	=>	array ('0'),
		'1'	=>	array ('1', '¹'),
		'2'	=>	array ('2', '²'),
		'3'	=>	array ('3', '³'),
		'4'	=>	array ('4'),
		'5'	=>	array ('5'),
		'6'	=>	array ('6'),
		'7'	=>	array ('7'),
		'8'	=>	array ('8'),
		'9'	=>	array ('9'),
	);

	//	If the database encoding isn't UTF-8 and multibyte string functions are available, try converting the text to UTF-8
	if ((empty($modSettings['global_character_set']) || $modSettings['global_character_set'] !== 'UTF-8') && function_exists('mb_convert_encoding'))
		$text = mb_convert_encoding($text, 'UTF-8', 'auto');

	//	Change the entities back to normal characters
	$text = str_replace('&amp;', '&', $text);
	$text = str_replace('&quot;', '"', $text);
	$prettytext = '';

	//	Split up $text into UTF-8 letters
	preg_match_all("~.~su", $text, $characters);
	foreach ($characters[0] as $aLetter)
	{
		foreach ($characterHash as $replace => $search)
		{
			//	Found a character? Replace it!
			if (in_array($aLetter, $search))
			{
				$prettytext .= $replace;
				break;
			}
		}
	}
	//	Remove unwanted '-'s
	$prettytext = preg_replace(array('~^-+|-+$~', '~-+~'), array('', '-'), $prettytext);
	return $prettytext;
}

//	Synchronise the topic URLs, fixing any differences between the topics and pretty_topic_urls tables
function synchroniseTopicUrls()
{
	global $db_prefix, $modSettings;

	//	Get the current database pretty URLs and other stuff
	$query = db_query("
		SELECT t.ID_TOPIC, t.ID_BOARD, t.pretty_url, m.subject, p.ID_BOARD as ID_BOARD2, p.pretty_url as pretty_url2
		FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
			LEFT JOIN {$db_prefix}pretty_topic_urls AS p ON (t.ID_TOPIC = p.ID_TOPIC)
		WHERE m.ID_MSG = t.ID_FIRST_MSG", __FILE__, __LINE__);

	$topicData = array();
	$oldUrls = array();
	$tableTopics = array();
	$tablePretty = array();

	//	Fill the $topicData array
	while ($row = mysql_fetch_assoc($query))
	{
		$topicData[] = array(
			'ID_TOPIC' => $row['ID_TOPIC'],
			'ID_BOARD' => $row['ID_BOARD'],
			'ID_BOARD2' => isset($row['ID_BOARD2']) ? $row['ID_BOARD2'] : 0,
			'pretty_url' => $row['pretty_url'],
			'pretty_url2' => isset($row['pretty_url2']) ? $row['pretty_url2'] : '',
			'subject' => $row['subject']
		);
		$oldUrls[] = $row['pretty_url'];
		$oldUrls[] = $row['pretty_url2'];
	}
	mysql_free_result($query);

	//	Go through the $topicData array and fix anything that needs fixing
	foreach ($topicData as $row)
	{
		//	Both empty? Then get a new pretty URL :)
		if ($row['pretty_url'] == '' && $row['pretty_url2'] == '')
		{
			//	A topic in the recycle board deserves only a blank URL
			$pretty_text = $modSettings['recycle_enable'] && $row['ID_BOARD'] == $modSettings['recycle_board'] ? '' : substr(generatePrettyUrl($row['subject']), 0, 80);
			//	Can't be empty, can't be a number and can't be the same as another
			if ($pretty_text == '' || is_numeric($pretty_text) || array_search($pretty_text, $oldUrls) != 0)
				//	Add suffix '-tID_TOPIC' to the pretty url
				$pretty_text = substr($pretty_text, 0, 70) . ($pretty_text != '' ? '-t' : 't') . $row['ID_TOPIC'];

			//	Update the arrays
			$tableTopics[] = array(
				'ID_TOPIC' => $row['ID_TOPIC'],
				'pretty_url' => $pretty_text
			);
			$tablePretty[] = '(' . $row['ID_TOPIC'] . ', ' . $row['ID_BOARD'] . ', "' . $pretty_text . '")';
			$oldUrls[] = $pretty_text;
		}
		//	First is empty, so use the second
		elseif ($row['pretty_url'] == '')
		{
			$tableTopics[] = array(
				'ID_TOPIC' => $row['ID_TOPIC'],
				'pretty_url' => $row['pretty_url2']
			);
			$tablePretty[] = '(' . $row['ID_TOPIC'] . ', ' . $row['ID_BOARD'] . ', "' . $row['pretty_url2'] . '")';
		}
		//	If the pretty URLs or the board IDs don't match, use the first
		elseif ($row['pretty_url'] =! $row['pretty_url2'] || $row['ID_BOARD'] =! $row['ID_BOARD2'])
		{
			$tableTopics[] = array(
				'ID_TOPIC' => $row['ID_TOPIC'],
				'pretty_url' => $row['pretty_url']
			);
			$tablePretty[] = '(' . $row['ID_TOPIC'] . ', ' . $row['ID_BOARD'] . ', "' . $row['pretty_url'] . '")';
		}
	}

	//	Update the database
	foreach ($tableTopics as $row)
		db_query("
			UPDATE {$db_prefix}topics
			SET pretty_url = '" . $row['pretty_url'] . "'
			WHERE ID_TOPIC = " . $row['ID_TOPIC'], __FILE__, __LINE__);
	if (count($tablePretty) > 0)
		db_query("
			REPLACE INTO {$db_prefix}pretty_topic_urls
				(ID_TOPIC, ID_BOARD, pretty_url)
			VALUES " . implode(',', $tablePretty), __FILE__, __LINE__);
}

//	Update the database based on the installed filters
function updateFilters()
{
	global $sourcedir, $context, $modSettings, $filterSettings, $db_prefix;

	//	Get the filter and htaccess settings
	require_once($sourcedir . '/PrettyUrls-Filters.php');
	$filterSettings =  filterAndHtaccessSettings();

	//	Update the settings table
	ksort($filterSettings['filters']);
	updateSettings(array('pretty_filter_callbacks' => serialize($filterSettings['filters'])));

	//	Clear the URLs cache
	db_query("
		TRUNCATE TABLE {$db_prefix}pretty_urls_cache", __FILE__, __LINE__);

	//	Don't rewrite anything for this page
	$modSettings['pretty_enable_filters'] = false;
}

?>
