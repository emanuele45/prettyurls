The Pretty URLs mod needs to make several changes to the database. This is a reference in case you need to manually edit or fix something.

If you ever want to completely remove the mod, you will need to remove all of these additions too. To be super-cool, use the [uninstall tool](http://prettyurls.googlecode.com/svn/trunk/tools/uninstall.php).

# New tables #

| **Name** | **Structure** | **Description** |
|:---------|:--------------|:----------------|
| `smf_pretty_topic_urls` | `id_topic`: [mediumint](http://dev.mysql.com/doc/refman/5.0/en/numeric-types.html), `pretty_url`: [varchar(80)](http://dev.mysql.com/doc/refman/5.0/en/char.html) | Stores the (partial) URLs for topics. Currently the only way to change a topic's URL is to edit this table manually. |
| `smf_pretty_urls_cache` | `url_id`: [varchar(255)](http://dev.mysql.com/doc/refman/5.0/en/char.html), `replacement`: [varchar(255)](http://dev.mysql.com/doc/refman/5.0/en/char.html) | A complete list of every URL in the forum with its prettified replacement. The `url_id` column stores the original URL, with `$scripturl` and other common URLs shortened. |

# New settings #

Settings are stored in the `smf_settings` table. Many of these settings use serialized arrays, so the following PHP functions might be useful: [serialize()](http://php.net/serialize), [unserialize()](http://php.net/unserialize).

| **Name** | **Description** |
|:---------|:----------------|
| `pretty_action_array` | An array of forum actions, extracted from index.php. |
| `pretty_board_lookup` | A serialized array of `URL => ID_BOARD` pairs, used for processing `$_GET['board']`. There can be more than one URL for each board. |
| `pretty_board_urls` | A serialized array of `ID_BOARD => URL` pairs, used for generating pretty replacement URLs. |
| `pretty_enable_filters` | Sets whether to prettify anything at all. 0 for no, 1 for yes. |
| `pretty_filters` | A serialized array of [filter data](Filters.md). |
| `pretty_filter_callbacks` | A serialized array of callback function names, sorted by priority. Don't manually edit this, edit the `pretty_filters` setting instead. |
| `pretty_root_url` | The base URL used by the board and topic filters, which by default is the same as `$boardurl`. |