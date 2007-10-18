<?php
//	Version: 0.8; Subs-PrettyUrls

if (!defined('SMF'))
	die('Hacking attempt...');

//	Generate a pretty URL from a given text
function pretty_generate_url($text)
{
	global $modSettings;

	//	Do you know your ABCs?
	$characterHash = array (
		'a'	=>	array ('a', 'A', 'à', 'À', 'á', 'Á', 'â', 'Â', 'ã', 'Ã', 'ä', 'Ä', 'å', 'Å', 'ª', 'ą', 'Ą', 'а', 'А', 'ạ', 'Ạ', 'ả', 'Ả', 'Ầ', 'ầ', 'Ấ', 'ấ', 'Ậ', 'ậ', 'Ẩ', 'ẩ', 'Ẫ', 'ẫ', 'Ă', 'ă', 'Ắ', 'ắ', 'Ẵ', 'ẵ', 'Ặ', 'ặ', 'Ằ', 'ằ', 'Ẳ', 'ẳ', 'あ', 'ア', 'α', 'Α'),
		'aa'	=>	array ('ا'),
		'ae'	=>	array ('æ', 'Æ', 'ﻯ'),
		'and'	=>	array ('&'),
		'at'	=>	array ('@'),
		'b'	=>	array ('b', 'B', 'б', 'Б', 'ب'),
		'ba'	=>	array ('ば', 'バ'),
		'be'	=>	array ('べ', 'ベ'),
		'bi'	=>	array ('び', 'ビ'),
		'bo'	=>	array ('ぼ', 'ボ'),
		'bu'	=>	array ('ぶ', 'ブ'),
		'c'	=>	array ('c', 'C', 'ç', 'Ç', 'ć', 'Ć', 'č', 'Č'),
		'cent'	=>	array ('¢'),
		'ch'	=>	array ('ч', 'Ч', 'χ', 'Χ'),
		'chi'	=>	array ('ち', 'チ'),
		'copyright'	=>	array ('©'),
		'd'	=>	array ('d', 'D', 'Ð', 'д', 'Д', 'د', 'ض', 'đ', 'Đ', 'δ', 'Δ'),
		'da'	=>	array ('だ', 'ダ'),
		'de'	=>	array ('で', 'デ'),
		'degrees'	=>	array ('°'),
		'dh'	=>	array ('ذ'),
		'do'	=>	array ('ど', 'ド'),
		'e'	=>	array ('e', 'E', 'è', 'È', 'é', 'É', 'ê', 'Ê', 'ë', 'Ë', 'ę', 'Ę', 'е', 'Е', 'ё', 'Ё', 'э', 'Э', 'Ẹ', 'ẹ', 'Ẻ', 'ẻ', 'Ẽ', 'ẽ', 'Ề', 'ề', 'Ế', 'ế', 'Ệ', 'ệ', 'Ể', 'ể', 'Ễ', 'ễ', 'え', 'エ', 'ε', 'Ε'),
		'f'	=>	array ('f', 'F', 'ф', 'Ф', 'ﻑ', 'φ', 'Φ'),
		'fu'	=>	array ('ふ', 'フ'),
		'g'	=>	array ('g', 'G', 'ğ', 'Ğ', 'г', 'Г', 'γ', 'Γ'),
		'ga'	=>	array ('が', 'ガ'),
		'ge'	=>	array ('げ', 'ゲ'),
		'gh'	=>	array ('غ'),
		'gi'	=>	array ('ぎ', 'ギ'),
		'go'	=>	array ('ご', 'ゴ'),
		'gu'	=>	array ('ぐ', 'グ'),
		'h'	=>	array ('h', 'H', 'ح', 'ه'),
		'ha'	=>	array ('は', 'ハ'),
		'half'	=>	array ('½'),
		'he'	=>	array ('へ', 'ヘ'),
		'hi'	=>	array ('ひ', 'ヒ'),
		'ho'	=>	array ('ほ', 'ホ'),
		'i'	=>	array ('i', 'I', 'ì', 'Ì', 'í', 'Í', 'î', 'Î', 'ï', 'Ï', 'ı', 'İ', 'и', 'И', 'Ị', 'ị', 'Ỉ', 'ỉ', 'Ĩ', 'ĩ', 'い', 'イ', 'η', 'Η', 'Ι', 'ι'),
		'j'	=>	array ('j', 'J', 'ج'),
		'ji'	=>	array ('じ', 'ぢ', 'ジ', 'ヂ'),
		'k'	=>	array ('k', 'K', 'к', 'К', 'ك', 'κ', 'Κ'),
		'ka'	=>	array ('か', 'カ'),
		'ke'	=>	array ('け', 'ケ'),
		'kh'	=>	array ('х', 'Х', 'خ'),
		'ki'	=>	array ('き', 'キ'),
		'ko'	=>	array ('こ', 'コ'),
		'ku'	=>	array ('く', 'ク'),
		'l'	=>	array ('l', 'L', 'ł', 'Ł', 'л', 'Л', 'ل', 'λ', 'Λ'),
		'la'	=>	array ('ﻻ'),
		'm'	=>	array ('m', 'M', 'м', 'М', 'م', 'μ', 'Μ'),
		'ma'	=>	array ('ま', 'マ'),
		'me'	=>	array ('め', 'メ'),
		'mi'	=>	array ('み', 'ミ'),
		'mo'	=>	array ('も', 'モ'),
		'mu'	=>	array ('む', 'ム'),
		'n'	=>	array ('n', 'N', 'ñ', 'Ñ', 'ń', 'Ń', 'н', 'Н', 'ن', 'ん', 'ン', 'ν', 'Ν'),
		'na'	=>	array ('な', 'ナ'),
		'ne'	=>	array ('ね', 'ネ'),
		'ni'	=>	array ('に', 'ニ'),
		'no'	=>	array ('の', 'ノ'),
		'nu'	=>	array ('ぬ', 'ヌ'),
		'o'	=>	array ('o', 'O', 'ò', 'Ò', 'ó', 'Ó', 'ô', 'Ô', 'õ', 'Õ', 'ö', 'Ö', 'ø', 'Ø', 'º', 'о', 'О', 'Ọ', 'ọ', 'Ỏ', 'ỏ', 'Ộ', 'ộ', 'Ố', 'ố', 'Ỗ', 'ỗ', 'Ồ', 'ồ', 'Ổ', 'ổ', 'Ơ', 'ơ', 'Ờ', 'ờ', 'Ớ', 'ớ', 'Ợ', 'ợ', 'Ở', 'ở', 'Ỡ', 'ỡ', 'お', 'オ', 'ο', 'Ο', 'ω', 'Ω'),
		'p'	=>	array ('p', 'P', '%', 'п', 'П', 'π', 'Π'),
		'pa'	=>	array ('ぱ', 'パ'),
		'pe'	=>	array ('ぺ', 'ペ'),
		'pi'	=>	array ('ぴ', 'ピ'),
		'plus'	=>	array ('+'),
		'plusminus'	=>	array ('±'),
		'po'	=>	array ('ぽ', 'ポ'),
		'pound'	=>	array ('£'),
		'ps'	=>	array ('ψ', 'Ψ'),
		'pu'	=>	array ('ぷ', 'プ'),
		'q'	=>	array ('q', 'Q', 'ق'),
		'quarter'	=>	array ('¼'),
		'r'	=>	array ('r', 'R', '®', 'р', 'Р', 'ر'),
		'ra'	=>	array ('ら', 'ラ'),
		're'	=>	array ('れ', 'レ'),
		'ri'	=>	array ('り', 'リ'),
		'ro'	=>	array ('ろ', 'ロ'),
		'ru'	=>	array ('る', 'ル'),
		's'	=>	array ('s', 'S', 'ş', 'Ş', 'ś', 'Ś', 'с', 'С', 'س', 'ص', 'š', 'Š', 'σ', 'ς', 'Σ'),
		'sa'	=>	array ('さ', 'サ'),
		'se'	=>	array ('せ', 'セ'),
		'section'	=>	array ('§'),
		'sh'	=>	array ('ш', 'Ш', 'ش'),
		'shi'	=>	array ('し', 'シ'),
		'shch'	=>	array ('щ', 'Щ'),
		'so'	=>	array ('そ', 'ソ'),
		'ss'	=>	array ('ß'),
		'su'	=>	array ('す', 'ス'),
		't'	=>	array ('t', 'T', 'т', 'Т', 'ت', 'ط', 'τ', 'Τ', 'ţ', 'Ţ'),
		'ta'	=>	array ('た', 'タ'),
		'te'	=>	array ('て', 'テ'),
		'th'	=>	array ('ث', 'θ', 'Θ'),
		'three-quarters'	=>	array ('¾'),
		'to'	=>	array ('と', 'ト'),
		'ts'	=>	array ('ц', 'Ц'),
		'tsu'	=>	array ('つ', 'ツ'),
		'u'	=>	array ('u', 'U', 'ù', 'Ù', 'ú', 'Ú', 'û', 'Û', 'ü', 'Ü', 'у', 'У', 'Ụ', 'ụ', 'Ủ', 'ủ', 'Ũ', 'ũ', 'Ư', 'ư', 'Ừ', 'ừ', 'Ứ', 'ứ', 'Ự', 'ự', 'Ử', 'ử', 'Ữ', 'ữ', 'う', 'ウ', 'υ', 'Υ'),
		'v'	=>	array ('v', 'V', 'в', 'В', 'β', 'Β'),
		'w'	=>	array ('w', 'W', 'و'),
		'wa'	=>	array ('わ', 'ワ'),
		'wo'	=>	array ('を', 'ヲ'),
		'x'	=>	array ('x', 'X', '×', 'ξ', 'Ξ'),
		'y'	=>	array ('y', 'Y', 'ý', 'Ý', 'ÿ', 'й', 'Й', 'ы', 'Ы', 'ي', 'Ỳ', 'ỳ', 'Ỵ', 'ỵ', 'Ỷ', 'ỷ', 'Ỹ', 'ỹ'),
		'ya'	=>	array ('я', 'Я', 'や'),
		'yen'	=>	array ('¥'),
		'yo'	=>	array ('よ'),
		'yu'	=>	array ('ю', 'Ю', 'ゆ'),
		'z'	=>	array ('z', 'Z', 'ż', 'Ż', 'ź', 'Ź', 'з', 'З', 'ز', 'ظ', 'ž', 'Ž', 'ζ', 'Ζ'),
		'za'	=>	array ('ざ', 'ザ'),
		'ze'	=>	array ('ぜ', 'ゼ'),
		'zh'	=>	array ('ж', 'Ж'),
		'zo'	=>	array ('ぞ', 'ゾ'),
		'zu'	=>	array ('ず', 'づ', 'ズ', 'ヅ'),
		'-'	=>	array ('-', ' ', '.', ','),
		'_'	=>	array ('_'),
		'!'	=>	array ('!'),
		'~'	=>	array ('~'),
		'*'	=>	array ('*'),
		chr(18)	=>	array ("'", '"', 'ﺀ', 'ع'),
		'('	=>	array ('(', '{', '['),
		')'	=>	array (')', '}', ']'),
		'$'	=>	array ('$'),
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

	$text = preg_replace('/&#(\d+);/me', "chr(\\1)", $text);

	//	If the database encoding isn't UTF-8 and multibyte string functions are available, try converting the text to UTF-8
	if ((empty($modSettings['global_character_set']) || $modSettings['global_character_set'] !== 'UTF-8') && function_exists('mb_convert_encoding'))
		$text = mb_convert_encoding($text, 'UTF-8');

	//	Change the entities back to normal characters
	$text = str_replace(array('&amp;', '&quot;'), array('&', '"'), $text);
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

//	Build the table of pretty topic URLs
//	This function used to do a lot more, but I kept the name the same though now it doesn't
function pretty_synchronise_topic_urls()
{
	global $db_prefix, $modSettings, $smfFunc;

	//	Get the current database pretty URLs and other stuff
	$query = $smfFunc['db_query']('', "
		SELECT t.id_topic, t.id_board, m.subject, p.pretty_url
		FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
			LEFT JOIN {$db_prefix}pretty_topic_urls AS p ON (t.id_topic = p.id_topic)
		WHERE m.id_msg = t.id_first_msg", __FILE__, __LINE__);

	$topicData = array();
	$oldUrls = array();
	$tablePretty = array();

	//	Fill the $topicData array
	while ($row = $smfFunc['db_fetch_assoc']($query))
	{
		$topicData[] = array(
			'id_topic' => $row['id_topic'],
			'id_board' => $row['id_board'],
			'pretty_url' => isset($row['pretty_url']) ? $row['pretty_url'] : '',
			'subject' => $row['subject']
		);
		$oldUrls[] = $row['pretty_url'];
	}
	$smfFunc['db_free_result']($query);

	//	Go through the $topicData array and fix anything that needs fixing
	foreach ($topicData as $row)
	{
		//	No pretty URL? That's ghastly!
		if ($row['pretty_url'] == '')
		{
			//	A topic in the recycle board deserves only a blank URL
			$pretty_text = $modSettings['recycle_enable'] && $row['id_board'] == $modSettings['recycle_board'] ? '' : substr(pretty_generate_url($row['subject']), 0, 80);
			//	Can't be empty, can't be a number and can't be the same as another
			if ($pretty_text == '' || is_numeric($pretty_text) || array_search($pretty_text, $oldUrls) != 0)
				//	Add suffix '-tID_TOPIC' to the pretty url
				$pretty_text = substr($pretty_text, 0, 70) . ($pretty_text != '' ? '-t' : 't') . $row['ID_TOPIC'];

			//	Update the arrays
			$tablePretty[] = '(' . $row['id_topic'] . ', "' . $pretty_text . '")';
			$oldUrls[] = $pretty_text;
		}
	}

	//	Update the database
	if (count($tablePretty) > 0)
		$smfFunc['db_query']('', "
			REPLACE INTO {$db_prefix}pretty_topic_urls
				(id_topic, pretty_url)
			VALUES " . implode(', ', $tablePretty), __FILE__, __LINE__);
}

//	Update the database based on the installed filters and build the .htaccess file
function pretty_update_filters()
{
	global $modSettings, $db_prefix, $boarddir, $boardurl, $smfFunc;

	//	Get the settings
	$prettyFilters = unserialize($modSettings['pretty_filters']);
	$filterSettings = array();
	$rewrites = array();
	foreach ($prettyFilters as $filter)
		//	Get the important data from enabled filters
		if ($filter['enabled'])
		{
			if (isset($filter['filter']))
				$filterSettings[$filter['filter']['priority']] = $filter['filter']['callback'];
			if (isset($filter['rewrite']))
				$rewrites[$filter['rewrite']['priority']] = array(
					'id' => $filter['id'],
					'rule' => $filter['rewrite']['rule'],
				);
		}

	//	Backup the current .htaccess file
	@copy($boarddir . '/.htaccess', $boarddir . '/.htaccess.backup-' . date('Y-m-d'));

	//	Build the new .htaccess file
	$htaccess = '#	Pretty URLs mod
#	http://code.google.com/p/prettyurls/
#	.htaccess file generated automatically on: ' . date('F j, Y, G:i') . '

RewriteEngine on';

	ksort($rewrites);
	foreach ($rewrites as $rule)
		$htaccess .= '

#	Rules for: ' . $rule['id'] . '
' . $rule['rule'];

	//	Fix the Root URL
	if (preg_match('~' . $boardurl . '/(.*)~', $modSettings['pretty_root_url'], $match))
		$htaccess = str_replace('ROOTURL', $match[1] . '/', $htaccess);
	else
		$htaccess = str_replace('ROOTURL', '', $htaccess);

	//	Output the file
	$handle = fopen($boarddir . '/.htaccess', 'w');
	fwrite($handle, $htaccess);
	fclose($handle);

	//	Update the settings table
	ksort($filterSettings);
	updateSettings(array('pretty_filter_callbacks' => serialize($filterSettings)));

	//	Clear the URLs cache
	$smfFunc['db_query']('', "
		TRUNCATE TABLE {$db_prefix}pretty_urls_cache", __FILE__, __LINE__);

	//	Don't rewrite anything for this page
	$modSettings['pretty_enable_filters'] = false;
}

?>
