{include file='header.tpl'}

	<p>
    {if !$show_limit}
    Dies ist deine kombinierte Karte:
    {else}
    Du kannst dieses Tool nur einmal pro Tag ausführen! Dies ist deine Karte von heute:
    {/if}
    </p>
    
    <img src="{$root_path}cache/map_combine/dsplus_map_{$pic_hash}.png" alt="dsplus karten, überlagert" border="0" />

    

{include file='footer.tpl'}