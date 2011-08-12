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

</div>

</body>
</html>