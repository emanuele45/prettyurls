# Introduction #

**Pretty URLs** is a URL management package for [Simple Machine Forums](http://simplemachines.org) (SMF).

It is simple, powerful, extensible and free. Most of all, it is pretty! Its main purpose is to rewrite the important SMF URLs, like these:

  * `http://domain.com/board/topic/`
  * `http://domain.com/profile/user/`
  * `http://domain.com/unreadreplies/`

Pretty URLs will work with SMF 1.1 and 2.0. It requires an Apache webserver with support for mod\_rewrite and .htaccess files.

Pretty URLs is released under a [new BSD licence](http://prettyurls.googlecode.com/svn/trunk/LICENCE)<br>
Copyright (c) 2006-2010 <a href='http://prettyurls.googlecode.com/svn/trunk/CONTRIBUTORS'>The Pretty URLs Contributors</a>

<h1>Downloads</h1>

You can download packages from the <a href='http://code.google.com/p/prettyurls/downloads/list'>Downloads</a> tab at the top! Use the latest version of the <code>prettyurls</code> package. Sometimes snapshot packages are available too: these have extra features currently in development, and should only be installed by advanced users.<br>
<br>
An <code>extras</code> package is also available, which contains some additional filters. The <code>reverter</code> package should be installed if you decide to remove the prettyurls package: it will redirect the pretty URLs back to the normal SMF urls.<br>
<br>
<h1>Installation</h1>

To install the mod:<br>
<br>
<ul><li>First check again that your server supports mod_rewrite and .htaccess files.<br>
</li><li>A UTF-8 database encoding is recommended, though not required. Certain functions will work unreliably with other encodings.<br>
</li><li>Download and install the <code>prettyurls</code> package in your forum's package manager.<br>
</li><li>Enable URL rewriting in the new Pretty URLs page.</li></ul>

<h1>Need help?</h1>

<b><a href='TroubleShooting.md'>Check the trouble shooting page first!</a></b>

If that doesn't answer your questions, ask for help in the <a href='http://www.simplemachines.org/community/index.php?topic=146969'>support topic</a>. Please post live URLs which we can easily access. It's too difficult to help with forums installed locally or on intranets.<br>
<br>
<h1>How to contribute</h1>

If you have sufficient PHP skills, try fixing a bug. We're on the look out for more developers.<br>
<br>
If you speak a language other than English, please help by translating the mod. We use <a href='http://translations.launchpad.net/prettyurls'>Launchpad.net</a> for translations.