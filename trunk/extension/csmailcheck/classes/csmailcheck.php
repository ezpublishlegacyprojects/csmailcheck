<?php
/**
 * Mail check temporary storing, based on user cookie logins are fetched
 * 
 * */


class CSMailCheck extends eZPersistentObject{
				
	function CSMailCheck( $row )
    {
        $this->eZPersistentObject( $row );
    }
	 
    static function definition()
    {
     
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "expires" => array( 'name' => 'Expires',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ), 
                                         "login" => array( 'name' => 'Login',
                                                        'datatype' => 'string',
                                                        'default' => '',
                                                        'required' => true ),                                        
                                         "passwd" => array( 'name' => "Password",
                                                                'datatype' => 'string',
                                                                'default' => 0,
                                                                'required' => true ),  
                                         "hash" => array( 'name' => "Hash",
                                                                'datatype' => 'string',
                                                                'default' => 0,
                                                                'required' => false ),  
                                         "imap" => array( 'name' => "Imap",
                                                                'datatype' => 'string',
                                                                'default' => '',
                                                                'required' => false )
                                                              ),
                      'function_attributes' => array( ),
                      "keys" => array( "id" ),                  
                      "increment_key" => "id",
                      "class_name" => "CSMailCheck",
                      "name" => "csmailcheck" );
                      
    }
    
        
    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( CSMailCheck::definition(),
                                                null,
                                                array( "id" => $id ),
                                                $asObject );
    }
      
    function fetchByHash( $hash, $asObject = true)
    {        
        return eZPersistentObject::fetchObject( CSMailCheck::definition(),
                                                null,
                                                array( "hash" => $hash ),
                                                $asObject );
    }   
         
    /**
     * Is cookie hash belongs to user, security check
     * */
    function verifyHash()
    {
        $RemoteAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $Hash = sha1($RemoteAddress.$_SERVER['HTTP_USER_AGENT'].$this->attribute('imap').$this->attribute('login').$this->attribute('passwd'));
    
        return $this->attribute('hash') == $Hash;
    }
}

?>