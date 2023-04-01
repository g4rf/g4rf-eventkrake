# eventkrake3-wp-plugin
A wordpress plugin for maintaining events, locations and artists with a REST
endpoint.

* author: Jan Kossick <jankossick@online.de>
* doc: https://www.eventkrake.de

In der vorliegenden Form erweitert das Plugin nur den Adminbereich und ergänzt drei
[Custom Post Types](https://codex.wordpress.org/Post_Types).

Für die Darstellung im Frontend ist das Theme zuständig, das die Daten über die
[REST API](http://www.eventkrake.de/api/) oder direkt aus der Datenbank abrufen
kann.

Zusätzlich gibt es einen [Shortcode] (https://codex.wordpress.org/Shortcode_API)
um eine Eingabemaske im Frontend bereitzustellen. Der Shortcode lautet
[eventkrake_input]. Nähere Infos siehe weiter unten.

## Struktur
Die Struktur folgt dem eines [Wordpress Plugins](https://codex.wordpress.org/Writing_a_Plugin).
Die Dateien müssen im Ordner **/wp-content/plugins/g4rf_eventkrake3** abgelegt werden.

### g4rf_eventkrake3.php
Der Startpunkt für Wordpress mit Metainfos. Hier werden die benötigten Scripte geladen,
[Custom Post Types](https://codex.wordpress.org/Post_Types) festgelegt,  die Callbacks
zum Speichern der Metainfos für die Custom Post Types definiert und ein rudimentärer
[Shortcode](https://codex.wordpress.org/Shortcode_API) angelegt. Zusätzlich
werden hier die Routen und Endpoints für die REST API angelegt.

### Eventkrake.php
Enthält Hilfsfunktionen zum setzen von Post-Meta-Infos, zur Ausgabe von
Admin-Meldungen und weitere Hilfsfunktionen.

### meta_event.php
Enthält das HTML für die Metabox im Adminbereich "Veranstaltungen".

### meta_location.php
Enthält das HTML für die Metabox im Adminbereich "Orte".

### meta_artists.php
Enthält das HTML für die Metabox im Adminbereich "Künstler:innen".

## Shortcodes
Zur Zeit gibt es einen Shortcode.

### [eventkrake]
Lädt anhand von Attributen eine Anzahl von Events in den DOM als data-Attribute.
Sollte nur genutzt werden, wenn ein REST-Abruf der Daten nicht möglich ist.