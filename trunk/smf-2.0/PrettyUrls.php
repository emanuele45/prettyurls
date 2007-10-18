<?php
//	Version: 0.8; PrettyUrls

if (!defined('SMF'))
	die('Hacking attempt...');

//	Shell for all the Pretty URL interfaces
function PrettyInterface()
{
	global $context, $scripturl, $settings, $txt;

	//	Keep the critters out
	isAllowedTo('admin_forum');

	//	Default templating stuff
	loadTemplate('PrettyUrls');
	if (loadLanguage('PrettyUrls') == false)
		loadLanguage('PrettyUrls', 'english');

	//	Shiny chrome interface
	$context['template_layers'][] = 'pretty_chrome';
	$context['html_headers'] .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/pretty/chrome.css" media="screen,projection" />';
	$context['pretty']['chrome'] = array(
		'menu' => array(
			'settings' => array(
				'href' => $scripturl . '?action=admin;area=pretty',
				'title' => $txt['pretty_chrome_menu_settings'],
			),
			'maintenance' => array(
				'href' => $scripturl . '?action=admin;area=pretty;sa=maintenance',
				'title' => $txt['pretty_chrome_menu_maintenance'],
			),
		),
	);

	//	What can we do today?
	$subActions = array(
		'settings' => 'pretty_manage_settings',
		'maintenance' => 'pretty_manage_settings'
	);
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]))
		call_user_func($subActions[$_REQUEST['sa']]);
	else
		pretty_manage_settings();
}

//	An interface to manage the settings and filters
function pretty_manage_settings()
{
	global $context, $modSettings, $settings, $sourcedir, $txt;

	//	Core settings
	$context['pretty']['settings']['core'] = array(
		array(
			'id' => 'pretty_enable_filters',
			'label' => $txt['pretty_enable'],
			'type' => 'text',
			'value' => $modSettings['pretty_enable_filters'],
		),
	);

	//	Load the filters data
	$context['pretty']['filters'] = unserialize($modSettings['pretty_filters']);

	//	Are we saving settings now?
	if (isset($_REQUEST['save']))
	{
		foreach ($context['pretty']['filters'] as $filter)
			$context['pretty']['filters'][$filter['id']]['enabled'] = isset($_POST['pretty_filter_' . $filter['id']]) ? 1 : 0;

		$pretty_settings = array(
			'pretty_enable_filters' => $_POST['pretty_enable'],
			'pretty_filters' => addslashes(serialize($context['pretty']['filters'])),
		);
		updateSettings($pretty_settings);

		//	Update the filters too
		require_once($sourcedir . '/Subs-PrettyUrls.php');
		pretty_update_filters();

		redirectexit('action=admin;area=pretty;sa=settings');
	}

	//	Action-specific chrome
	$context['page_title'] = $txt['pretty_chrome_title_settings'];
	$context['sub_template'] = 'pretty_settings';
	$context['pretty']['chrome']['title'] = $txt['pretty_chrome_menu_settings'];
	$context['pretty']['chrome']['caption'] = $txt['pretty_chrome_caption_settings'];

	//	Load the settings up
	$context['pretty']['settings']['enable'] = $modSettings['pretty_enable_filters'];
}

?>
