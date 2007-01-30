<?php
//	Version: 0.4; Subs-PrettyUrls

if (!defined('SMF'))
	die('Hacking attempt...');

//	Generate a pretty URL from a given text
function generatePrettyUrl($text)
{
	static $characterHash = array (
		'a'	=>	array ('a', 'A', 'à', 'À', 'á', 'Á', 'â', 'Â', 'ã', 'Ã', 'ä', 'Ä', 'å', 'Å', 'ª', 'ą', 'Ą', 'а', 'А'),
		'b'	=>	array ('b', 'B', 'б', 'Б'),
		'c'	=>	array ('c', 'C', 'ç', 'Ç', 'ć', 'Ć'),
		'd'	=>	array ('d', 'D', 'Ð', 'д', 'Д'),
		'e'	=>	array ('e', 'E', 'è', 'È', 'é', 'É', 'ê', 'Ê', 'ë', 'Ë', 'ę', 'Ę', 'е', 'Е', 'ё', 'Ё', 'э', 'Э'),
		'f'	=>	array ('f', 'F', 'ф', 'Ф'),
		'g'	=>	array ('g', 'G', 'ğ', 'Ğ', 'г', 'Г'),
		'h'	=>	array ('h', 'H'),
		'i'	=>	array ('i', 'I', 'ì', 'Ì', 'í', 'Í', 'î', 'Î', 'ï', 'Ï', 'ı', 'İ', 'и', 'И'),
		'j'	=>	array ('j', 'J'),
		'k'	=>	array ('k', 'K', 'к', 'К'),
		'l'	=>	array ('l', 'L', 'ł', 'Ł', 'л', 'Л'),
		'm'	=>	array ('m', 'M', 'м', 'М'),
		'n'	=>	array ('n', 'N', 'ñ', 'Ñ', 'ń', 'Ń', 'н', 'Н'),
		'o'	=>	array ('o', 'O', 'ò', 'Ò', 'ó', 'Ó', 'ô', 'Ô', 'õ', 'Õ', 'ö', 'Ö', 'ø', 'Ø', 'º', 'о', 'О'),
		'p'	=>	array ('p', 'P', 'п', 'П'),
		'q'	=>	array ('q', 'Q'),
		'r'	=>	array ('r', 'R', '®', 'р', 'Р'),
		's'	=>	array ('s', 'S', 'ş', 'Ş', 'ś', 'Ś', 'с', 'С'),
		't'	=>	array ('t', 'T', 'т', 'Т'),
		'u'	=>	array ('u', 'U', 'ù', 'Ù', 'ú', 'Ú', 'û', 'Û', 'ü', 'Ü', 'µ', 'у', 'У'),
		'v'	=>	array ('v', 'V', 'в', 'В'),
		'w'	=>	array ('w', 'W'),
		'x'	=>	array ('x', 'X', '×'),
		'y'	=>	array ('y', 'Y', 'ý', 'Ý', 'ÿ', 'й', 'Й', 'ы', 'Ы'),
		'z'	=>	array ('z', 'Z', 'ż', 'Ż', 'ź', 'Ź', 'з', 'З'),
		'-'	=>	array ('-', ' ', '.', ','),
		'_'	=>	array ('_'),
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
		'ae'	=>	array ('æ', 'Æ'),
		'and'	=>	array ('&'),
		'at'	=>	array ('@'),
		'cent'	=>	array ('¢'),
		'ch'	=>	array ('ч', 'Ч'),
		'copyright'	=>	array ('©'),
		'degrees'	=>	array ('°'),
		'dollar'	=>	array ('$'),
		'half'	=>	array ('½'),
		'kh'	=>	array ('х', 'Х'),
		'percent'	=>	array ('%'),
		'plus'	=>	array ('+'),
		'plusminus'	=>	array ('±'),
		'pound'	=>	array ('£'),
		'quarter'	=>	array ('¼'),
		'section'	=>	array ('§'),
		'sh'	=>	array ('ш', 'Ш'),
		'shch'	=>	array ('щ', 'Щ'),
		'ss'	=>	array ('ß'),
		'three-quarters'	=>	array ('¾'),
		'ts'	=>	array ('ц', 'Ц'),
		'ya'	=>	array ('я', 'Я'),
		'yen'	=>	array ('¥'),
		'yu'	=>	array ('ю', 'Ю'),
		'zh'	=>	array ('ж', 'Ж'),
	);

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
//			Found a character? Replace it!
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
	$newData = array();
	$oldUrls = array();

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
//		Both empty? Then get a new pretty URL :)
		if ($row['pretty_url'] == '' && $row['pretty_url2'] == '')
		{
//			A topic in the recycle board deserves only a blank URL
			$pretty_text = $modSettings['recycle_enable'] && $row['ID_BOARD'] == $modSettings['recycle_board'] ? '' : substr(generatePrettyUrl($row['subject']), 0, 80);
//			Can't be empty, can't be a number and can't be the same as another
			if ($pretty_text == '' || is_numeric($pretty_text) || array_search($pretty_text, $oldUrls) != 0)
//				Add suffix '-tID_TOPIC' to the pretty url
				$pretty_text = substr($pretty_text, 0, 70) . ($pretty_text != '' ? '-t' : 't') . $row['ID_TOPIC'];

//			Update the arrays
			$newData[] = array(
				'ID_TOPIC' => $row['ID_TOPIC'],
				'ID_BOARD' => $row['ID_BOARD'],
				'pretty_url' => $pretty_text
			);
			$oldUrls[] = $pretty_text;
		}
//		First is empty, so use the second
		elseif ($row['pretty_url'] == '')
			$newData[] = array(
				'ID_TOPIC' => $row['ID_TOPIC'],
				'ID_BOARD' => $row['ID_BOARD'],
				'pretty_url' => $row['pretty_url2']
			);	
//		If the pretty URLs or the board IDs don't match, use the first
		elseif ($row['pretty_url'] =! $row['pretty_url2'] || $row['ID_BOARD'] =! $row['ID_BOARD2'])
			$newData[] = array(
				'ID_TOPIC' => $row['ID_TOPIC'],
				'ID_BOARD' => $row['ID_BOARD'],
				'pretty_url' => $row['pretty_url']
			);	
	}

//	Update the database
	foreach ($newData as $row)
	{
		db_query("
			UPDATE {$db_prefix}topics
			SET pretty_url = '" . $row['pretty_url'] . "'
			WHERE ID_TOPIC = " . $row['ID_TOPIC'], __FILE__, __LINE__);
		db_query("
			REPLACE INTO {$db_prefix}pretty_topic_urls (ID_TOPIC, ID_BOARD, pretty_url) 
			VALUES (" . $row['ID_TOPIC'] .", " . $row['ID_BOARD'] . ", '" . $row['pretty_url'] . "')", __FILE__, __LINE__);
	}
}

?>
