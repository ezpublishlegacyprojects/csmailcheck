<?php

class CSMailCheckOperators
{
    
    function operatorList()
    {
        return array( 'csmailchecklink');
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {   
        	
        return array( 	   'csmailchecklink' =>  array (	'params' => array( 'type' => 'array',
                                                          	'required' => true,
                                                          	'default' => array() )
                                                          	)                                                 
                                                 
                                                 );
    }

    function modify( $tpl, $operatorName, $operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
                
        switch ( $operatorName )
        {
            // Returns formated link for direct mail read, for example in squiremail
            case 'csmailchecklink':
            {     
                $INI   = eZINI::instance( 'csmailcheck.ini' );	
                $params = $namedParameters['params'];                              
                $predefinedLink = $INI->variable('Webclient_'.$params['host'], 'address');                
                $operatorValue = str_replace(array('{messageUniqueID}','{msgNumber}'),array($params['msg_id'],$params['msg_number']),$predefinedLink);                	
                      
            } break;
                        	
        }
    }
}

?>