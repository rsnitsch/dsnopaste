# README #

DS NoPaste ist eine in PHP programmierte Webseite mit einigen Tools für das Browsergame DieStämme: https://www.die-staemme.de/

### Einrichtung ###

Eine detaillierte Anleitung folgt. Bis dahin gibt es nur die folgenden rudimentären Hinweise:

* Das Projekt ist nur in Kombination mit twdata voll funktionsfähig. twdata ist für den Weltdaten-Import zuständig. Siehe: https://bitbucket.org/rsnitsch/twdata
* Am einfachsten ist die Einrichtung mit einem User `nopaste` und einem User `twdata`.
* Unter `/home/nopaste/prod` kann man die produktive NoPaste-Installation anlegen, unter `/home/nopaste/test` ist eine Test-Installation empfehlenswert, an der man aktiv entwickeln kann.
* Unter `/home/twdata` sollte twdata eingerichtet werden. Die Einrichtung eines Cronjobs bietet sich an zur regelmäßigen Aktualisierung der Weltdaten.
    * In die `config.ini` von twdata (einfach `config.sample.ini` kopieren) müssen die korrekten Daten eingetragen werden.
    * Das Kernstück von twdata ist myupdater.py. Diesem Skript werden per Kommandozeile die Welten-Kürzel übergeben, deren Daten man importieren möchte. Zum Beispiel `myupdater.py de124`. Alternativ kann man auch nur das Skript aufrufen, ohne Parameter. Das Skript holt sich dann die zu importierenden Welten von DS NoPaste's Server-Verzeichnis; es werden also einfach alle Welten aktualisiert, die in NoPaste aktiv sind. Dazu muss aber in der `config.ini` der korrekte Pfad zu NoPaste eingetragen sein.
    * twdata benötigt zwei Unterordner mit Schreibzugriff: `cache` und `data`.
    * Der Datenbank-Server benötigt unbedingt Zugriff auf das `data`-Verzeichnis von twdata. Unter Umständen wird das erschwert durch apparmor, welches inzwischen bei vielen Linux-Distributionen standardmäßig aktiv ist.
* Zur Einrichtung von NoPaste selbst:
    * Im Unterordner include muss eine Datei `localconfig.inc.php` erstellt werden. Als Vorbild kann die Datei `config.local.sample.inc.php` dienen. Alle möglichen Konfigurations-Parameter können in der config.inc.php nachgeschaut werden (darin sind auch alle Standardwerte ersichtlich). In der `localconfig.inc.php` können alle diese Parameter nach Belieben gesetzt (und damit überschrieben) werden.
    * Im Unterordner `install` befindet sich das SQL-Datenbankschema für NoPaste.
    * Auf den Unterordner `data` muss der Webserver Schreibzugriff haben.

### Lizenz ###

Die Veröffentlichung erfolgt unter der GPL 3.0 Lizenz (siehe gpl-3.0.txt).
