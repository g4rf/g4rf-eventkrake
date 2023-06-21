# Eventkrake WP Plugin
A wordpress plugin for maintaining events, locations and artists with a REST
endpoint.

* author: Jan Kossick <admin@g4rf.net>
* doc: https://www.eventkrake.de

In der vorliegenden Form erweitert das Plugin nur den Adminbereich und ergänzt drei
[Custom Post Types](https://codex.wordpress.org/Post_Types).

Für die Darstellung im Frontend ist das Theme zuständig, das die Daten über die
[REST API](http://www.eventkrake.de/api/) oder direkt durch Aufruf der 
Eventkrake-Klassen abrufen kann.


## Struktur
Die Struktur folgt dem eines [Wordpress Plugins](https://codex.wordpress.org/Writing_a_Plugin).
Die Dateien müssen im Ordner **/wp-content/plugins/g4rf-eventkrake** abgelegt werden.

### g4rf-eventkrake.php
Der Startpunkt für Wordpress mit Metainfos. Hier werden die benötigten Scripte geladen,
[Custom Post Types](https://codex.wordpress.org/Post_Types) festgelegt,  die Callbacks
zum Speichern der Metainfos für die Custom Post Types definiert und ein rudimentärer
[Shortcode](https://codex.wordpress.org/Shortcode_API) angelegt. Zusätzlich
werden hier die Routen und Endpoints für die REST API angelegt.

### meta_event.php
Enthält das HTML für die Metabox im Adminbereich "Veranstaltungen".

### meta_location.php
Enthält das HTML für die Metabox im Adminbereich "Orte".

### meta_artists.php
Enthält das HTML für die Metabox im Adminbereich "Künstler:innen".


## Klassen

### \Eventkrake\Eventkrake
Die statische Klasse `Eventkrake` enthält statische Hilfsfunktionen zum Setzen 
von Post-Meta-Infos, zur Ausgabe von Admin-Meldungen und weitere Hilfsfunktionen.

### \Eventkrake\Event

Stellt einzelne Events dar. Zum Erstellen sollte die statische Funktion
`Event::Factory($id)` genutzt werden, da somit pro Termin ein Event erzeugt
und alle Termine als Array zurückgegeben werden.

### \Eventkrake\Location

Erzeugt ein Location-Objekt.

### \Eventkrake\Artist

Erzeugt ein Artist-Objekt.


## Shortcodes
Zur Zeit gibt es einen Shortcode.

### [eventkrake]
Lädt anhand von Attributen eine Anzahl von Events in den DOM als data-Attribute.
Sollte nur genutzt werden, wenn ein REST-Abruf der Daten nicht möglich ist.