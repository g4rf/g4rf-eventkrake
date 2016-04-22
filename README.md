# eventkrake2-wp-plugin
Ein Wordpress-Plugin um Orte und Veranstaltungen mit Wordpress zu verwalten und mit der Eventkrake 
zu synchronisieren.

* Autor: Jan Kossick <jankossick@online.de>
* Webseite: http://www.eventkrake.de

In der vorliegenden Form erweitert das Plugin nur den Adminbereich und ergänzt zwei 
[Custom Post Types](https://codex.wordpress.org/Post_Types). Beim Speichern der Posts
werden die Daten über die [Eventkrake 2 API](http://www.eventkrake.de/api/) mit der 
Eventkrake synchronisiert, **vorrausgesetzt es liegt eine gültige E-Mail-Adresse samt
Schlüssel vor**.

Für die Darstellung im Frontend ist das Theme zuständig, das ebenfalls über die 
[Eventkrake 2 API](http://www.eventkrake.de/api/) die Daten abrufen kann. **Dafür
ist keine Authentifizierung per E-Mail und Schlüssel notwendig**.

## Struktur
Die Struktur folgt dem eines [Wordpress Plugins](https://codex.wordpress.org/Writing_a_Plugin).
Die Dateien müssen im Ordner **/wp-content/plugins/g4rf_eventkrake2** abgelegt werden.

### g4rf_eventkrake2.php
Der Startpunkt für Wordpress mit Metainfos. Hier werden die benötigten Scripte geladen,
[Custom Post Types](https://codex.wordpress.org/Post_Types) festgelegt,  die Callbacks 
zum Speichern der Metainfos für die Custom Post Types definiert und ein rudimentärer 
[Shortcode](https://codex.wordpress.org/Shortcode_API) angelegt. Zuletzt wird eine Seite
für Einstellungen dem Admin-Menü hinzugefügt.

### Eventkrake.php
Enthält Hilfsfunktionen zum setzen von Post-Meta-Infos, zum Abruf der [Eventkrake 2 API]
(http://www.eventkrake.de/api/), zur Ausgabe von Admin-Meldungen und weitere Hilfsfunktionen.

### meta_event.php
Enthält das HTML für die Metabox im Adminbereich "Veranstaltungen".

### meta_location.php
Enthält das HTML für die Metabox im Adminbereich "Orte".

### settings.php
Enthält die Struktur und Funktionen zum Speichern der Plugin-Einstellungen.
