<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc"><h4>{'Mail check'|i18n( 'csmailcheck/infobox' )}</h4>
    <div id="infobox-csmailcheck">
        <img src={"images/csmailcheck/loading.gif"|ezdesign()} alt="" title="" />
    </div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>     
<script type="text/javascript">
    csmailcheck.setPath({"/"|ezurl()}); 
    csmailcheck.setLoadingImage('{"images/csmailcheck/loading.gif"|ezdesign(no)}');         
    csmailcheck.initMail();
</script>