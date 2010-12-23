{include file='header.tpl'}

<h3 style="margin-top: 10px;">Konfiguration</h3>

    <p>
        Website aktiviert: {$cfg_enabled}<br />
        Website hochgeladen: {$cfg_uploaded}<br />
        Debugmodus aktiviert: {$cfg_debugmode}
    </p>
    
<h3 style="margin-top: 10px;">Angriffspläne</h3>

    <p>Anzahl Pläne: {$count}</p>
    
    <table border="0" class="admin">
        <tr>
            <th>ID</th>
            <th>Server</th>
            <th>IP</th>
            <th>Key</th>
            <th style="color: #21CC21; width: 130px;">Aktionen</th>
        </tr>
        {foreach item=plan from=$plans}
        <tr>
            <td>{$plan.id}</td>
            <td>{$plan.server}</td>
            <td>{$plan.ip}</td>
            <td>{$plan.key}</td>
            <td><a href="../tools/attplaner.php?id={$plan.id}&amp;key={$plan.key}" target="_blank">Anzeigen</a></td>
        </tr>
        {/foreach}
    </table>
    
{literal}
<!--
<script language="javascript" type="text/javascript">function deleteConfirm() { if(confirm("Diesen Eintrag wirklich löschen?")) return true; else return false; }</script>
-->
{/literal}

{include file='footer.tpl'}