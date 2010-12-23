{include file='header.tpl'}

<form action="{$world_url}/game.php?village={$from|intval}&amp;screen=place&amp;try=confirm" method="post" name="sendtroops" id="form_sendtroops" target="_self">
    <input type="hidden" name="x" value="{$to_x|intval}" />
    <input type="hidden" name="y" value="{$to_y|intval}" />
    {foreach from=$troops item=unit}
    <input type="hidden" name="{$unit.unitname|urlencode}" value="{$unit.count|intval}" />
    {/foreach}
    <input type="hidden" name="attack" value="Angreifen" />
    <input type="submit" value="Abschicken" />
</form>

<script language="javascript" type="text/javascript">
// <![CDATA[ {literal}
$(document).ready(function() {
    if(String(top.location).match(/target=top/i)) {
        $("#form_sendtroops").attr("target", "_top");
    }
    
    document.forms.sendtroops.submit();
});
// ]]> {/literal}
</script>

{include file='footer.tpl'}