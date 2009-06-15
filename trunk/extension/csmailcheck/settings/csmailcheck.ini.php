<?php /*

[CSMailCheck]
# Should we let user choose prefered imap server
ImapServersChoose=enabled

# Default imap server if server choose is disabled
DefaultImapServer=localhost

# How many messages we should show in infobox ?
MessageCount=5

# Availale imap servers
ImapServers[]
ImapServers[localhost]=Server name

# Only show inbox content without links to webmail client. Also no javascript is sended to backed
WebmailClient=disabled

[CSMailCheckCache]
# Should infobox be cache, turn of then developing
InfoboxCache=enabled

# Caching engine
# memcache,plain or apc possible
CachingEngine=memcache

[CachingOptions_plain]
path=var/cache/csmailcheck
options[]
# Cache expire in seconds
options[ttl]=30
class=ezcCacheStorageFilePlain

[CachingOptions_memcache]
path=memcache
options[]
# Cache expire in seconds
options[ttl]=30
options[host]=localhost
options[port]=11211
class=ezcCacheStorageMemcachePlain

[CachingOptions_apc]
path=apc
options[]
# Cache expire in seconds
options[ttl]=30
class=ezcCacheStorageApcPlain

# Based on server automaticly is choosen application
# Local host implementation using squremail as backed webmail
[Webclient_localhost]
# Link address then user clicks on infobox mail link
address=http://192.168.1.2/webmail/plugins/csmailcheck/webmail.php?right_frame=read_body.php?mailbox=INBOX&passed_id={messageUniqueID}&startMessage=1

# Authentificaiton request then user logins
# This file is included dynamicaly then user get logged
authentificationRequestUrl=http://192.168.1.2/webmail/plugins/csmailcheck/initsession.php?username={username}&password={password}

*/ ?>