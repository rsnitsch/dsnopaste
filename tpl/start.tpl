{extends 'layout.tpl'}
{block 'content'}

{if $cfg.enabled}
	<p>
	Willkommen zum DS NoPaste Service.<br />
	Diese Website beinhaltet einige Tools für das Browsergame <a href="http://www.die-staemme.de" target="_blank">DieStämme</a>.
	Besonders der Angriffsplaner hat sich als exzellentes Werkzeug erwiesen!
	</p>
		
		{*
		{if isset($ad)}
		<p class="small">Wenn du die Werbung ganz oben nicht mehr sehen möchtest, klicke <a href="index.php?hide_ad">hier</a>.</p>
		{/if}
		*}
		
		<h3>Tools</h3>
		
		<div id="tool_liste">
				<ul>
					<li><a id="attplaner" href="tools/attplaner.php" target="_self">Angriffsplaner</a></li>
					<li><a id="deffformular" href="tools/deffform.php" target="_self">Deffformular</a></li>
					<li><a id="deffformular_10plus" href="tools/deffform.php?s=modern" target="_self">Deffformular</a> (Welt 10+)</li>
					<li>
						<a id="farmmanager" href="tools/farmmanager.php" target="_self">Farmmanager</a>
						<ul>
							<li><a class="simple" href="http://forum.die-staemme.de/showthread.php?p=1350486#post1350486" target="_blank">Per Tastendruck Berichte einlesen!</a> <span class="tiny">(nur Firefox!)</span></li>
						</ul>
					</li>
					<li>
						<b>Extern: </b><a id="ext_berichteformatierer" href="http://bericht.terenceds.de/?lang=de">Berichteformatierer von terence</a>
						(Ergänzung zum Deffformular)
					</li>
				</ul>
		</div>

	<h3 style="text-decoration: none;">
		<a href="http://forum.np.bmaker.net/viewforum.php?id=9" target="_blank">News</a>
		(<a class="simple" style="padding-left: 22px; background: url('{$root_path}images/feed-icon.png') no-repeat scroll left center transparent;" href="http://forum.np.bmaker.net/extern.php?action=feed&fid=9&type=rss"><span class="tiny">RSS</span></a>)
	</h3>

	{foreach from=$news_items item=news_item}
	<div>
		<span class="bold">[{$news_item.date}]</span> - <a href="{$news_item.link}" target="_blank">{$news_item.title}</a><br />
		{$news_item.intro}
		<p>[<a class="simple" href="{$news_item.link}" target="_blank">mehr</a>]</p>
	</div>
	{/foreach}

{else}
	Dieser Service ist momentan deaktiviert. Evtl. werden Wartungsarbeiten durchgeführt.
{/if}

{/block}
