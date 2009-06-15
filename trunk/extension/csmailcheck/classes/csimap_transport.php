<?php
/**
 * Just MailImap transport extending for one functions witch gets new messages
 * 
 * */

class csMailImapTransport extends  ezcMailImapTransport
{
    public function getNewMessagesID( $offset, $count = 0, $sortCriteria, $reverse = false )
    {
        if ( $count < 0 )
        {
            throw new ezcMailInvalidLimitException( $offset, $count );
        }

        $range = array();
        if ( $this->options->uidReferencing )
        {
            $uids = array_values( $this->listUniqueIdentifiers() );

            $flip = array_flip( $uids );
            if ( !isset( $flip[$offset] ) )
            {
                throw new ezcMailOffsetOutOfRangeException( $offset, $count );
            }

            $start = $flip[$offset];

            $messages = $this->sort( $uids, $sortCriteria, $reverse );

            if ( $count === 0 )
            {
                $count = count( $messages );
            }

            $ids = array_keys( $messages );

            for ( $i = $start; $i < $count; $i++ )
            {
                $range[] = $ids[$i];
            }
        }
        else
        {
            $messageCount = $this->countByFlag( 'ALL' );
            $messages = array_keys( $this->sort( range( 1, $messageCount ), $sortCriteria, $reverse ) );

            if ( $count === 0 )
            {
                $count = count( $messages );
            }

            $range = array_slice( $messages, $offset - 1, $count, true );

            if ( !isset( $range[$offset - 1] ) )
            {
                throw new ezcMailOffsetOutOfRangeException( $offset, $count );
            }
        }

       return $range;
    }
}

?>