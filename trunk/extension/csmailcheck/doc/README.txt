Author
======
(C) 2009 JSC Coral solutions
Remigijus Kiminas
remdex@gmail.com
http://coralsolutions.com/

Description
===========
Contains a mail check widget witch can be implemented in site. Read more on projects.ez.no

Installation on eZ publish side
============

1. Create cache directory in ezPublish if you are using "CachingEngine=plain"
Directory path
var/cache/csmailcheck

2. Renegerate autoloads with:
php ./bin/php/ezpgenerateautoloads.php

3. Activate extension in ExtensionSettings section
ActiveExtensions[]=csmailcheck

4. Grant anonymous function mailcheck/chech. Clear all cache.

5. Tweak csmailcheck.ini.php according to your parameters

6. Include extension in page layout like this,
{include uri='design:csmailcheck/infobox.tpl'}

That's all. Enjoy :)

===============
Installing squiremail plugin

1. Copy csmailcheck from (csmailcheck/doc/plugins/squiremail/csmailcheck) to squiremail plugins directory (/usr/share/squirrelmail/plugins)
2. Tweak links in csmailcheck.ini.php "Webclient_localhost" section.
3. Enabled WebClient variable. This variable forces link generation in list.
WebmailClient=enabled