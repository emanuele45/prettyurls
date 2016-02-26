Pretty URLs is organised around filters, which are sets of instructions about what to do to with a page's URLs. If you want to change how Pretty URLs operates, or to add extra functionality, you will need to understand how a filter is constructed.

# The filters array #

The data for each filter Pretty URLs uses is all contained in a single array, which is [serialized](http://php.net/serialize) and stored in the `pretty_filters` database setting. You can edit this database setting yourself, however to make it easier to do so, a hidden admin tool was created which can be accessed at `index.php?action=admin;area=pretty;sa=filters` or `admin/?area=pretty;sa=filters` if the actions filter is enabled. This tool displays the array in the [JSON](http://www.json.org/) format which should be easier to edit, however the parser is still rather strict.

So what does the array contain? Well the best way to explain is with an example filter. Here is a filter in the PHP array format which, for example, you might add with a package:

```
$prettyFilters = array(
	'example' = array(	// The filter must have an ID which is also the array's key
		'description' => 'A new example filter',	// This description will be displayed in the admin panel
		'enabled' => 1,	// Set enabled to 1 to enable and 0 to disable it
		'filter' => array(	// Filter callbacks are functions which are run to process each URL in a page
			'priority' => 5,
			'callback' => 'pretty_example_filter',	// Name of the function to run
		),
		'rewrite' => array(	// Most filters will add rewrite rules which will change a pretty URL into a less pretty, but more useable, internal URL
			'priority' => 5,
			'rule' => 'RewriteRule ^example\.html index.php?action=example [L]',	// The RewriteRule to add to the .htaccess file, if there are multiple rules for a filter this can be an array of strings
		),
		'test_callback' = > 'pretty_example_test',	// A function which returns an short array of links which will be rewritten by this filter
		'title' => 'Example',	// This title will be displayed in the admin panel
	),
);
```

Each filter may have filter or rewrite sections. If it does, then each must have unique priority numbers, though a mod may use the same number for both it's filter callback and rewrite rules. These priority numbers are sorted in ascending order. Custom filters should use numbers less than 20 if they want to catch URLs before any of the standard filters do, or numbers more than 80 if they want to catch URLs only if the other filters have ignored them.

Here is the above array in JSON format:

```
{
	"example": {
		"description": "A new example filter",
		"enabled": 1,
		"filter": {
			"priority": 5,
			"callback": "pretty_example_filter"
		},
		"rewrite": {
			"priority": 5,
			"rule": "RewriteRule ^example\.html index.php?action=example [L]"
		},
		"test_callback": "pretty_example_test",
		"title": "Example"
	}
}
```

# Filter callbacks #

Filter callback functions do the work of rewriting a page's URLs. The list of URLs is passed to the function which can then change them however it likes. Usually various regexs will be used to search for query string fragments to replace. Here is an example function which would produce the URL used in the above filter:

```
function pretty_example_filter($urls)
{
	global $boardurl, $scripturl;

	$pattern = '`' . $scripturl . '?action=example$`S';	// The pattern to search for, in this case action=example, use ` so that ~s in URLs will work.
	$replacement = $boardurl . '/example.html';	// The replacement URL
	foreach ($urls as $url_id => $url)
		if (!isset($url['replacement']))	// This is very important: unless you are very certain of what you're doing, do not rewrite URLs that have already been rewritten!
			if (preg_match($pattern, $url['url']))
				$urls[$url_id]['replacement'] = preg_replace($pattern, $replacement, $url['url']);
	return $urls;
}
```

# Test callbacks #

Test callback functions return a short array of links which will be written by the filter.

```
function pretty_example_test()
{
	global $scripturl, $txt;

	return array('<a href="' . $scripturl . '?action=example">' . $txt['example_link'] . '</a>');
}
```