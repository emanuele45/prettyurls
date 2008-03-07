<?php
//	Version: 0.8; PrettyUrls

//	Pretty URLs chrome
function template_pretty_chrome_above()
{
	global $context, $txt;

	echo '
<div id="chrome">
	<h1>', $txt['pretty_chrome_title'], '</h1>
	<ul id="chrome_menu">';

	//	The subactions menu
	foreach ($context['pretty']['chrome']['menu'] as $id => $item)
		echo '
		<li><a href="', $item['href'], '" class="', $id, '" title="', $item['title'], '"><span>', $item['title'], '</span></a></li>';

	echo '
	</ul>
	<h2>', $context['pretty']['chrome']['title'], '</h2>
	<p id="chrome_caption">', $context['pretty']['chrome']['caption'], '</p>
	<div id="chrome_main">';
}

function template_pretty_chrome_below()
{
	echo '
	</div>
</div>';
}

//	It should be easy and fun to manage this mod
function template_pretty_settings()
{
	global $context, $scripturl, $txt;

	echo '
		<form id="adminsearch" action="', $scripturl, '?action=admin;area=pretty;save" method="post" accept-charset="', $context['character_set'], '">
			<fieldset>
				<legend>', $txt['pretty_core_settings'], '</legend>
				<label for="pretty_enable">', $txt['pretty_enable'], '</label>
				<input type="hidden" name="pretty_enable" value="0" />
				<input type="checkbox" name="pretty_enable" id="pretty_enable"', ($context['pretty']['settings']['enable'] ? ' checked="checked"' : ''), ' />
			</fieldset>
			<fieldset>
				<legend>', $txt['pretty_filters'], '</legend>';

	//	Display the filters
	foreach ($context['pretty']['filters'] as $id => $filter)
		echo '
				<div>
					<input type="checkbox" name="pretty_filter_', $id, '" id="pretty_filter_', $id, '"', ($filter['enabled'] ? ' checked="checked"' : ''), ' />
					<label for="pretty_filter_', $id, '">', $filter['title'], '</label>
					<p>', $filter['description'], '</p>
				</div>';

	echo '
			</fieldset>
			<fieldset>
				<input type="submit" value="', $txt['pretty_save'], '">
			</fieldset>
		</form>';
}

//	Forum out of whack?
function template_pretty_maintenance()
{
	global $context, $scripturl, $txt;

	if (isset($context['pretty']['maintenance_tasks']))
	{
		echo '
		<ul>';
		foreach ($context['pretty']['maintenance_tasks'] as $task)
			echo '
			<li>', $task, '</li>';
		echo '
		</ul>';
	}
	else
		echo '
		<p><a href="', $scripturl, '?action=admin;area=pretty;sa=maintenance;run">', $txt['pretty_run_maintenance'], '</a></p>';
}

?>
