{include file='header.tpl'}

	<p>
    Viele Spieler haben sich darüber beschwert, dass man seit DS Plus v3 nur noch 3 Stämme auf der Weltkarte markieren kann.
    <br />
    Mit dem "Map Combiner" kann man mit ein paar Mausklicks mehrere Karten überlagern und somit bis zu 9 Stämme markieren!
    </p>
    <p>
    <span class="warnung">Achtung: </span><b>Dieses Tool darf pro Tag nur einmal ausgeführt werden (pro Person)!</b>
    </p>
	<br />
	
	<form action="map_combine.php?merge" enctype="multipart/form-data" name="fupload" method="post">
        Karte 1: <input name="karte1" type="file" size="50" maxlength="30000" accept="image/png">
        <br />
        Karte 2: <input name="karte2" type="file" size="50" maxlength="30000" accept="image/png">
        <br />
        Karte 3: <input name="karte3" type="file" size="50" maxlength="30000" accept="image/png"> (optional)
        <br />
        <input type="submit" value="Karten überlagern!">
	</form>

    

{include file='footer.tpl'}