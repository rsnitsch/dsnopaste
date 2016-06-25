# README #

DS NoPaste ist eine in PHP programmierte Webseite mit einigen Tools für das Browsergame DieStämme.

### Einrichtung ###

Eine detaillierte Anleitung folgt. Bis dahin gibt es nur die folgenden rudimentären Hinweise:

* Das Projekt ist nur in Kombination mit twdata voll funktionsfähig. twdata ist für den Weltdaten-Import zuständig. Siehe: https://bitbucket.org/rsnitsch/twdata
* Am einfachsten ist die Einrichtung mit einem User "nopaste" und einem User "twdata".
* Unter /home/nopaste/prod kann man die produktive NoPaste-Installation anlegen, unter /home/nopaste/test ist eine Test-Installation empfehlenswert, an der man aktiv entwickeln kann.
* Unter /home/twdata sollte twdata eingerichtet werden. Die Einrichtung eines Cronjobs bietet sich an zur regelmäßigen Aktualisierung der Weltdaten.
* Das Kernstück von twdata ist myupdater.py. Diesem Skript werden per Kommandozeile die Welten-Kürzel übergeben, deren Daten man importieren möchte. Zum Beispiel "myupdater.py de21". Alternativ lässt sich in Zeile 14 des Skripts der Ordner von NoPaste mit den Welten-Informationen angeben. Dann werden einfach alle Welten aktualisiert, die gerade in NoPaste aktiv sind.
* Zur Einrichtung von NoPaste selbst:
    * Im Unterordner include muss eine Datei "localconfig.inc.php" erstellt werden. Als Vorbild kann die Datei config.local.sample.inc.php dienen. Alle möglichen Konfigurations-Parameter können in der config.inc.php nachgeschaut werden (darin sind auch alle Standardwerte ersichtlich). In der localconfig.inc.php können alle diese Parameter nach Belieben gesetzt (und damit überschrieben) werden.
    * Im Unterordner install befindet sich das SQL-Datenbankschema für NoPaste.
    * Auf den Unterordner data muss der Webserver Schreibzugriff haben.