If you're experiencing problems with Pretty URLs this page may help you. But first check that your forum is using the latest version of this mod. Stable and SVN snapshot packages are available on the [downloads page](http://code.google.com/p/prettyurls/downloads/list).

# Every link causes 404 Not Found errors #

If after installing the mod every link is broken, there is probably a problem with your server (it may not support mod\_rewrite or per-directory .htaccess configuration files). To fix the links so that the problem can be diagnosed and fixed, or so you can uninstall the mod, create a new .php file with this code:

```
<?php
require_once(dirname(__FILE__) . '/SSI.php');
updateSettings(array('pretty_enable_filters' => '0'));
?>
```

Upload the file to the same location as your forum's SSI.php and open it with your web browser. Then don't forget to delete it!

# Links point to old domain after moving forum #

This is very simple to fix, in addition to updating all the other settings with [repair\_settings.php](http://docs.simplemachines.org/index.php?topic=663), this mod has one more setting to fix. You can either manually fix the pretty\_root\_url setting yourself, or else create a new .php with this code:

```
<?php
require_once(dirname(__FILE__) . '/SSI.php');
require_once($sourcedir . '/Subs-PrettyUrls.php');
updateSettings(array('pretty_root_url' => $boardurl));
pretty_update_filters();
?>
```

Upload the file to the same location as your forum's SSI.php and open it with your web browser. Then don't forget to delete it!

# Sub-directories can't be opened #

If you have a sub-directory in your forum directory you will no longer be able to access it as SMF will think you want to open a board with its name (note if you refer to a page in that sub-directory it will work fine.) With one addition to your .htaccess file you can fix this. You may have to change your FTP client's settings in order to see the hidden .htaccess file. This example is for the sub-directory /chat/, which should open /chat/index.html; add it to the top of your .htaccess file (_outside_ the Pretty URLs section!)

```
RewriteRule ^chat/?$ /chat/index.html [L,QSA]
```