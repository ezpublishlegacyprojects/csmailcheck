<?php

$http = eZHTTPTool::instance();

include_once( "kernel/common/template.php" );
$tpl = templateInit();	

$INI   = eZINI::instance( 'csmailcheck.ini' );	

if ( $INI->variable('CSMailCheck', 'ImapServersChoose') == 'enabled' )
    $Host = $http->postVariable('imapserver');
else 
    $Host = $INI->variable('CSMailCheck', 'DefaultImapServer');

$Username   = $http->postVariable('username');
$Password   = $http->postVariable('passwd');
$RememberMe = $http->postVariable('rememberme') == 'true' ? true : false;

$imap = new csMailImapTransport( $Host );

try {
    $Authenticated = $imap->authenticate( $Username, $Password );
}
catch (ezcMailTransportException $e) {
    $Authenticated = false;
}

if ( $Authenticated )
{
    $imap->selectMailbox( 'Inbox' );
     
    $RemoteAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    $Hash = sha1($RemoteAddress.$_SERVER['HTTP_USER_AGENT'].$Host.$Username.$Password);
    
    $Mail = CSMailCheck::fetchByHash($Hash);
        
    $ExpiresCookie = ($RememberMe === true) ? time() + 31*24*60*60 : 0;
    $ExpiresRecord = ($RememberMe === true) ? 7*24*60*60 : 24 * 60 * 60;
              
    if ( ($Mail instanceof CSMailCheck) )
    {               
       $Mail->setAttribute('expires',time() + $ExpiresRecord);       
              
    } else {
        $Mail = new CSMailCheck(
            array('expires' => time() + $ExpiresRecord,
                  'login'   => $Username,
                  'passwd'  => $Password,
                  'hash'    => $Hash,
                  'imap'    => $Host)
        );
        $Mail->store();
    }

    setcookie('CS_MAIL_HASH',$Hash,$ExpiresCookie,'/');
    
    
    $messages = $imap->getNewMessagesID( 1, $INI->variable('CSMailCheck', 'MessageCount'), "Date", true );
        
    $mails = array();
    $parser = new ezcMailParser();        
    foreach ($messages as $msgid)
    {
        $msg = $imap->top( $msgid );
        $lines = preg_split( "/\r\n|\n/", $msg );
        $msg = null;
        foreach ( $lines as $line )
          {
              // eliminate the line that contains "Content-Type" at it would throw
             // a notice for "multipart/related" (because the multipart object cannot
              // be created due to missing the body)
             if ( stripos( $line, "Content-Type:" ) === false )
              {
                  $msg .= $line . PHP_EOL;
              }
              else
              {
                  // insert code to analyse the Content-Type of the mail
                  // and add an "attachment" icon in case it is "multipart"
              }
        }
        $set = new ezcMailVariableSet( $msg );
        $mail = $parser->parseMail( $set );
        
        $uid = $imap->listUniqueIdentifiers();
                   
        $mails[] = array(  'number' => $msgid,
                           'id' => $uid[$msgid], //Unique messageID used in external application
                           'from' => array('name' => $mail[0]->from->name,'email' => $mail[0]->from->email),
                           'subject' => $mail[0]->subject,
                           'size' => $mail[0],
                           'received' => $mail[0]->timestamp
                         );            
    }
       
    $tpl->setVariable('messages',$mails);
    $tpl->setVariable('host',$Host);
     
    $EnabledWebmail = $INI->variable('CSMailCheck', 'WebmailClient');    
    $tpl->setVariable('webmail_enabled',$EnabledWebmail);
        
    $AuthentificatedScriptURL = '';
    if ($EnabledWebmail == 'enabled')
    {
        setcookie('CS_MAIL_CHECK_REMOTE',1,0,'/');
        $AuthentificatedScriptURL = str_replace(array('{username}','{password}'),array($Username,$Password),$INI->variable('Webclient_'.$Host, 'authentificationRequestUrl'));
    }  
    
    $CacheEngine = $INI->variable('CSMailCheckCache','CachingEngine');
    $Options = $INI->variable('CachingOptions_'.$CacheEngine,'options');
    // String parameters cannot be passed to ezcomponents, nonsense to convert to integer here....
    foreach ($Options as $key => $option)
    {
        if (in_array($key,array('ttl','port')))
        {
            $Options[$key] = (int)$option;
        }
    }

    ezcCacheManager::createCache( 'csmailcheck', $INI->variable('CachingOptions_'.$CacheEngine,'path'), $INI->variable('CachingOptions_'.$CacheEngine,'class'), $Options );

    $cache = ezcCacheManager::getCache( 'csmailcheck' );     
    $infoboxContent = $tpl->fetch( "design:csmailcheck/inbox.tpl" );
    $cache->store( $GLOBALS['eZCurrentAccess']['name'].$Hash, $infoboxContent ); 
      
    echo json_encode(array('result' => $infoboxContent ,'authentificationscript' => $AuthentificatedScriptURL, 'error' => 'false' ));
    
} else {    
    echo json_encode(array('result' => $tpl->fetch( "design:csmailcheck/error.tpl" ) , 'error' => 'true' ));
}

eZExecution::setCleanExit( );
exit;

?>