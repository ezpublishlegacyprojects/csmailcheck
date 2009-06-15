<table>
    {if eq( ezini( 'CSMailCheck', 'ImapServersChoose', 'csmailcheck.ini' ), 'enabled' )}
    <tr>
        <td>{'Server'|i18n( 'csmailcheck/loginform' )}</td>
    </tr>    
    <tr>
        <td>
            <select class="box" id="CSMailImapServer">
                {foreach ezini( 'CSMailCheck', 'ImapServers', 'csmailcheck.ini' ) as $mailServer => $nameServer}
                    <option value="{$mailServer}">{$nameServer}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    {/if}
    <tr>
        <td>{'Username'|i18n( 'csmailcheck/loginform' )}</td>
    </tr>
    <tr>
        <input class="box" type="text" id="CSMailUsername" value="" />
    </tr>
    <tr>
        <td>{'Password'|i18n( 'csmailcheck/loginform' )}</td>
    </tr>
    <tr>
        <input class="box" type="password" id="CSMailPasswd" value="" />
    </tr>
    <tr>
        <td><label>{'Remember me'|i18n( 'csmailcheck/loginform' )} <input type="checkbox" id="CSMailRemember" value="on" /></label></td>
    </tr>
    <tr>
        <td><input type="button" value="Login" class="defaultbutton" onclick="csmailcheck.login()" ></td>
    </tr>
</table>