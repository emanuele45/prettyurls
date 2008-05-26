<?php
//	Version: 0.9; PrettyUrls

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
	$context['template_layers']['pretty_chrome'] = 'pretty_chrome';
	$context['html_headers'] .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/pretty/chrome.css" media="screen,projection" />';
	$context['pretty']['chrome'] = array(
		'menu' => array(
			'news' => array(
				'href' => $scripturl . '?action=admin;area=pretty',
				'title' => $txt['pretty_chrome_menu_news'],
			),
			'settings' => array(
				'href' => $scripturl . '?action=admin;area=pretty;sa=settings',
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
		'filters' => 'pretty_edit_filters',
		'maintenance' => 'pretty_maintenance',
		'news' => 'pretty_news',
		'settings' => 'pretty_manage_settings',
	);
	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]))
		call_user_func($subActions[$_REQUEST['sa']]);
	else
		pretty_news();
}

//	News and mod information
function pretty_news()
{
	global $context, $scripturl, $settings, $txt;

	//	This page needs some extra javascript :)
	$context['html_headers'] .= '
	<script type="text/javascript">var chrome = {
		pmUrl: "' . $scripturl . '?action=admin;area=packages;sa=download;get;sesc=' . $context['session_id'] . ';package=",
		upgradeTxt: "' . $txt['pretty_upgrade'] . '"
	};</script>
	<script type="text/javascript" src="' . $settings['default_theme_url'] . '/pretty/chrome.js"></script>
	<script type="text/javascript" src="http://prettyurls.googlecode.com/svn/trunk/news.js"></script>';

	$context['page_title'] = $txt['pretty_chrome_page_title_news'];
	$context['sub_template'] = 'pretty_news';
	$context['pretty']['chrome']['title'] = $txt['pretty_chrome_title_news'];
	$context['pretty']['chrome']['caption'] = $txt['pretty_chrome_caption_news'];
}

//	An interface to manage the settings and filters
function pretty_manage_settings()
{
	global $context, $modSettings, $sourcedir, $txt;

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
		//	Get each filter from the form and save them
		foreach ($context['pretty']['filters'] as $id => $filter)
			$context['pretty']['filters'][$id]['enabled'] = isset($_POST['pretty_filter_' . $id]) ? 1 : 0;

		$pretty_settings = array(
			'pretty_enable_filters' => $_POST['pretty_enable'],
			'pretty_filters' => serialize($context['pretty']['filters']),
		);
		updateSettings($pretty_settings);

		//	Update the filters too
		require_once($sourcedir . '/Subs-PrettyUrls.php');
		pretty_update_filters();

		//	All finished now!
		$_SESSION['pretty']['notice'] = 'Settings saved';
		redirectexit('action=admin;area=pretty;sa=settings');
	}

	//	Action-specific chrome
	$context['page_title'] = $txt['pretty_chrome_page_title_settings'];
	$context['sub_template'] = 'pretty_settings';
	$context['pretty']['chrome']['title'] = $txt['pretty_chrome_menu_settings'];
	$context['pretty']['chrome']['caption'] = $txt['pretty_chrome_caption_settings'];

	//	Load the settings up
	$context['pretty']['settings']['enable'] = $modSettings['pretty_enable_filters'];

	//	Any notices?
	if (isset($_SESSION['pretty']['notice']))
	{
		$context['pretty']['chrome']['notice'] = $_SESSION['pretty']['notice'];
		unset($_SESSION['pretty']['notice']);
	}
}

//	Interface for URL maintenance
function pretty_maintenance()
{
	global $context, $sourcedir, $txt;

	//	Run the maintenance tasks
	if (isset($_REQUEST['run']))
	{
		require_once($sourcedir . '/Subs-PrettyUrls.php');
		pretty_run_maintenance();
	}

	//	Action-specific chrome
	$context['page_title'] = $txt['pretty_chrome_page_title_maintenance'];
	$context['sub_template'] = 'pretty_maintenance';
	$context['pretty']['chrome']['title'] = $txt['pretty_chrome_menu_maintenance'];
	$context['pretty']['chrome']['caption'] = $txt['pretty_chrome_caption_maintenance'];
}

//	Interface to edit the filters array
function pretty_edit_filters()
{
	global $context, $modSettings, $sourcedir, $txt;

	//	Check the JSON extension is installed
	if (!function_exists('json_encode'))
	{
		unset($context['template_layers']['pretty_chrome']);
		fatal_lang_error('pretty_no_json', false);
	}

	//	Save the filters array
	if (isset($_REQUEST['save']))
	{
		//	Try to process the edited JSON array
		$json_filters = (isset($_POST['pretty_json_filters'])) ? $_POST['pretty_json_filters'] : '';
		$json_filters = stripslashes($json_filters);
		$filters_array = json_decode($json_filters, true);

		//	Was that successful or not?
		if ($filters_array == NULL)
		{
			$_SESSION['pretty']['notice'] = 'There was an error with the JSON array you submitted';
			$_SESSION['pretty']['json_filters'] = $json_filters;
		}
		else
		{
			require_once($sourcedir . '/Subs-PrettyUrls.php');
			updateSettings(array('pretty_filters' => serialize($filters_array)));
			pretty_update_filters();
			$_SESSION['pretty']['notice'] = 'Filters saved and updated';
		}

		redirectexit('action=admin;area=pretty;sa=filters');
	}

	//	Action-specific chrome
	$context['page_title'] = $txt['pretty_chrome_page_title_filters'];
	$context['sub_template'] = 'pretty_filters';
	$context['pretty']['chrome']['title'] = $txt['pretty_chrome_title_filters'];
	$context['pretty']['chrome']['caption'] = $txt['pretty_chrome_caption_filters'];

	if (isset($_SESSION['pretty']['json_filters']))
	{
		//	We're working on something already
		$context['pretty']['json_filters'] = $_SESSION['pretty']['json_filters'];
		unset($_SESSION['pretty']['json_filters']);
	}
	else
	{
		//	Convert the filters array to JSON and format it nicely
		require_once($sourcedir . '/Subs-PrettyUrls.php');
		$context['pretty']['json_filters'] = json_encode(unserialize($modSettings['pretty_filters']));
		$context['pretty']['json_filters'] = pretty_json($context['pretty']['json_filters']);
		$context['pretty']['json_filters'] = str_replace('\/', '/', $context['pretty']['json_filters']);
	}

	//	Any new notices?
	if (isset($_SESSION['pretty']['notice']))
	{
		$context['pretty']['chrome']['notice'] = $_SESSION['pretty']['notice'];
		unset($_SESSION['pretty']['notice']);
	}
}

?>
