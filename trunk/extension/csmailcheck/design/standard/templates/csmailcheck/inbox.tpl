<div class="message-list-header"><h5>{'Messages'|i18n( 'csmailcheck/inbox' )}</h5></div>
<div class="csmail-msg-list">
    <ul class="csmail-list">
        {if gt($messages|count,0)}
            {foreach $messages as $msg}
                <li>{if eq($webmail_enabled,'enabled')}<a target="_blank" href="{csmailchecklink(hash('msg_id',$msg.id,'msg_number',$msg.number,'host',$host))}" title="{$msg.from.email}">{/if}<strong>{if ne($msg.from.name,'')}{$msg.from.name}{else}{$msg.from.email}{/if}<br /></strong>{$msg.subject}<br /><strong>[{$msg.received|datetime( 'custom', '%Y-%m-%d %H:%i:%s' )}]</strong>{if eq($webmail_enabled,'enabled')}</a>{/if}</li>
            {/foreach}
        {else}
            {'Empty...'|i18n( 'csmailcheck/inbox' )}
        {/if}
    </ul>
</div>
<div class="action-block">
    <input type="button" class="defaultbutton" onclick="csmailcheck.logout()" value="{'Logout'|i18n( 'csmailcheck/inbox' )}" />
</div>