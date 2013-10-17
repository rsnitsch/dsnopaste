{extends 'layout.tpl'}
{block 'content'}

{if $cfg.enabled}
	<p>
	Willkommen zum DS NoPaste Service.<br />
	Diese Website beinhaltet einige Tools für das Browsergame <a href="http://www.die-staemme.de" target="_blank">DieStämme</a>.
	Besonders der Angriffsplaner hat sich als exzellentes Werkzeug erwiesen!
	</p>

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
{else}
	Dieser Service ist momentan deaktiviert. Evtl. werden Wartungsarbeiten durchgeführt.
{/if}

{/block}
