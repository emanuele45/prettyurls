<?php

/*******************************************************************************
	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Pretty URLs Extras 0.9

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
}
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

require_once($sourcedir . '/Subs-PrettyUrls.php');

//	Add these filters
$prettyFilters = unserialize($modSettings['pretty_filters']);
//	A patch to fix the relative URLs used by the arcade mod
$prettyFilters['arcade'] = array(
	'description' => 'A patch for the arcade mod',
	'enabled' => 0,
	'rewrite' => array(
		'priority' => 70,
		'rule' => 'RewriteRule ^arcade/index\.php$ index.php?action=arcade [L,QSA]',
	),
	'title' => 'Arcade',
);
//	A redirection patch for the SEO4SMF mod
$prettyFilters['seo4smf'] = array(
	'description' => 'A patch to redirect pages from the SEO4SMF format to the Pretty URLs format',
	'enabled' => 0,
	'rewrite' => array(
		'priority' => 25,
		'rule' => array(
			'RewriteRule ^(.*)-b([0-9]*)\.([0-9]*)/;(.*) index.php?board=$2.$3;$4 [L,QSA]',
			'RewriteRule ^(.*)-b([0-9]*)\.([0-9]*)/$ index.php?board=$2.$3 [L,QSA]',
			'RewriteRule ^(.*)-b([0-9]*)\.([0-9])$ index.php?board=$2.$3 [L,QSA]',
			'RewriteRule ^(.*)-t([0-9]*)\.([0-9]*)\.html;((\?:from|msg|new)[0-9]*);(.*)$ index.php?topic=$2.$4;$6 [L,QSA]',
			'RewriteRule ^(.*)-t([0-9]*)\.([0-9]*)\.html;((\?:from|msg|new)[0-9]*) index.php?topic=$2.$4 [L,QSA]',
			'RewriteRule ^(.*)-t([0-9]*)\.([0-9]*)\.html;(.*)$ index.php?topic=$2.$3;$4 [L,QSA]',
			'RewriteRule ^(.*)-t([0-9]*)\.([0-9]*)\.html$ index.php?topic=$2.$3 [L,QSA]',
		),
	),
	'title' => 'SEO4SMF redirections',
);
//	Pretty URLs for Tiny Portal's articles
$prettyFilters['tp-articles'] = array(
	'description' => 'Rewrite Tiny Portal article URLs',
	'enabled' => 0,
	'filter' => array(
		'priority' => 30,
		'callback' => 'pretty_tp_articles_filter',
	),
	'rewrite' => array(
		'priority' => 30,
		'rule' => 'RewriteRule ^page/([^/]+)/?$ ./index.php?pretty;page=$1 [L,QSA]',
	),
	'title' => 'Tiny Portal articles',
);

updateSettings(array('pretty_filters' => isset($smcFunc) ? serialize($prettyFilters) : addslashes(serialize($prettyFilters))));

//	Update everything now
pretty_update_filters();

?>
