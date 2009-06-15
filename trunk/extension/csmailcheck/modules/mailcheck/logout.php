<?
$CSMailHash = $Params['HashCSMail'];

$Mail = CSMailCheck::fetchByHash($CSMailHash);
if ($Mail instanceof CSMailCheck)
{  
    $Mail->remove();
    setcookie('CS_MAIL_HASH','',1,'/');
    setcookie('CS_MAIL_CHECK_REMOTE','',1,'/');
    $INI   = eZINI::instance( 'csmailcheck.ini' );	
    
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
    $cache->delete( $GLOBALS['eZCurrentAccess']['name'].$CSMailHash );     
}

include_once( "kernel/common/template.php" );
$tpl = templateInit();	

echo json_encode(array('result' => $tpl->fetch( "design:csmailcheck/loginform.tpl" ) , 'error' => 'false' ));

eZExecution::setCleanExit( );
exit;
?>