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

//	It should be easy to manage this mod
function template_pretty_settings()
{
}

?>
