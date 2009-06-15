<?php
$INI   = eZINI::instance( 'csmailcheck.ini' );	

$CSMailHash = (isset($Params['HashCSMail']) && strlen($Params['HashCSMail']) == 40)  ? $Params['HashCSMail'] : false;

/*Retrvieve from cache if exists*/
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
  
if ( $CSMailHash === false || $INI->variable('CSMailCheckCache','InfoboxCache') == 'disabled' || ( $infoboxContent = $cache->restore( $GLOBALS['eZCurrentAccess']['name'].$CSMailHash ) ) === false )
{    

include_once( "kernel/common/template.php" );
$tpl = templateInit();	

$Mail = CSMailCheck::fetchByHash($CSMailHash);

if ( $Mail instanceof CSMailCheck && $Mail->verifyHash() )
{    
     
    $imap = new csMailImapTransport( $Mail->attribute('imap') );
    
    try {
        $Authenticated = $imap->authenticate( $Mail->attribute('login'), $Mail->attribute('passwd') );
    }
    catch (ezcMailTransportException $e) {
        $Authenticated = false;
    }    
    
    if ( $Authenticated )
    {
        $imap->selectMailbox( 'Inbox' );
        
        switch ( $INI->variable('CSMailCheck', 'FetchType') ) {
    	case 'date':
    		  $messages = $imap->getNewMessagesID( 1, $INI->variable('CSMailCheck', 'MessageCount'), "Date", true );
    		break;	
    		
    	case 'unseen':
    		  $messages = $imap->getUnseenMessages( $INI->variable('CSMailCheck', 'MessageCount') );
    		break;
    
    	default:
    	      $messages = array();
    		break;
        }
        
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
        $tpl->setVariable('host',$Mail->attribute('imap'));
        
        $EnabledWebmail = $INI->variable('CSMailCheck', 'WebmailClient');  
        $tpl->setVariable('webmail_enabled',$EnabledWebmail);
             
        $InitRemoteSession = (isset($_COOKIE['CS_MAIL_CHECK_REMOTE']) || $EnabledWebmail == 'disabled') ? true : false;
        $RemoteUnitCookie = '';
        
        if ($InitRemoteSession === false)
        {
            $RemoteUnitCookie = str_replace(array('{username}','{password}'),array($Mail->attribute('login'),$Mail->attribute('passwd')),$INI->variable('Webclient_'.$Mail->attribute('imap'), 'authentificationRequestUrl'));
            
            // Expires on session end
            setcookie('CS_MAIL_CHECK_REMOTE',1,0,'/');
        }
        
        $infoboxContent = $tpl->fetch( "design:csmailcheck/inbox.tpl" );
        
        $cache->store( $GLOBALS['eZCurrentAccess']['name'].$CSMailHash, $infoboxContent ); 
                
        echo json_encode(array('result' => $infoboxContent ,'authentificationscript' => $RemoteUnitCookie, 'error' => 'false' ));
        
    }    
    
} else {    
    echo json_encode(array('result' => $tpl->fetch( "design:csmailcheck/loginform.tpl" ) , 'error' => 'false' ));
}

} else {
     echo json_encode(array('result' => $infoboxContent ,'authentificationscript' => '', 'error' => 'false' ));
}

eZExecution::setCleanExit( );
exit;

?>