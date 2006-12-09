<?php
//	Version: 0.1; Subs-PrettyUrls

if (!defined('SMF'))
	die('Hacking attempt...');

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

	$prettytext = '';

	for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++)
	{
		foreach ($characterHash as $replace => $search)
		{
			if (in_array(mb_substr($text, $i, 1, 'UTF-8'), $search))
			{
				$prettytext .= $replace;
				break;
			}
		}
	}
	return $prettytext;
}

?>
