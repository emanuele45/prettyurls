<?php
//	Version: 0.1; Subs-PrettyUrls

if (!defined('SMF'))
	die('Hacking attempt...');

//	Generate a pretty URL from a given text
function generatePrettyUrl($text)
{
	static $characterHash = array (
		'a'	=>	array ('a', 'A', 'à', 'À', 'á', 'Á', 'â', 'Â', 'ã', 'Ã', 'ä', 'Ä', 'å', 'Å', 'ª'),
		'b'	=>	array ('b', 'B'),
		'c'	=>	array ('c', 'C', 'ç', 'Ç'),
		'd'	=>	array ('d', 'D', 'Ð'),
		'e'	=>	array ('e', 'E', 'è', 'È', 'é', 'É', 'ê', 'Ê', 'ë', 'Ë'),
		'f'	=>	array ('f', 'F'),
		'g'	=>	array ('g', 'G'),
		'h'	=>	array ('h', 'H'),
		'i'	=>	array ('i', 'I', 'ì', 'Ì', 'í', 'Í', 'î', 'Î', 'ï', 'Ï'),
		'j'	=>	array ('j', 'J'),
		'k'	=>	array ('k', 'K'),
		'l'	=>	array ('l', 'L'),
		'm'	=>	array ('m', 'M'),
		'n'	=>	array ('n', 'N', 'ñ', 'Ñ'),
		'o'	=>	array ('o', 'O', 'ò', 'Ò', 'ó', 'Ó', 'ô', 'Ô', 'õ', 'Õ', 'ö', 'Ö', 'ø', 'Ø', 'º'),
		'p'	=>	array ('p', 'P'),
		'q'	=>	array ('q', 'Q'),
		'r'	=>	array ('r', 'R', '®'),
		's'	=>	array ('s', 'S'),
		't'	=>	array ('t', 'T'),
		'u'	=>	array ('u', 'U', 'ù', 'Ù', 'ú', 'Ú', 'û', 'Û', 'ü', 'Ü', 'µ'),
		'v'	=>	array ('v', 'V'),
		'w'	=>	array ('w', 'W'),
		'x'	=>	array ('x', 'X', '×'),
		'y'	=>	array ('y', 'Y', 'ý', 'Ý', 'ÿ'),
		'z'	=>	array ('z', 'Z'),
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
		'copyright'	=>	array ('©'),
		'degrees'	=>	array ('°'),
		'dollar'	=>	array ('$'),
		'half'	=>	array ('½'),
		'percent'	=>	array ('%'),
		'plus'	=>	array ('+'),
		'plusminus'	=>	array ('±'),
		'pound'	=>	array ('£'),
		'quarter'	=>	array ('¼'),
		'section'	=>	array ('§'),
		'ss'	=> array ('ß'),
		'three-quarters'	=>	array ('¾'),
		'yen'	=>	array ('¥'),
	);

//	Change the entities back to normal characters
	$text = str_replace('&amp;', '&', $text);
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
	global $db_prefix;

//	Get the current database pretty URLs and other stuff
	$query = db_query("
		SELECT t.ID_TOPIC, t.ID_BOARD, t.pretty_url, m.subject, p.ID_BOARD as ID_BOARD2, p.pretty_url as pretty_url2
		FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
		LEFT JOIN {$db_prefix}pretty_topic_urls AS p
		ON t.ID_TOPIC = p.ID_TOPIC
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
			$pretty_text = substr(generatePrettyUrl($row['subject']), 0, 80);
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
