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

Zusätzlich gibt es einen [Shortcode] (https://codex.wordpress.org/Shortcode_API)
um eine Eingabemaske im Frontend bereitzustellen. Der Shortcode lautet 
[eventkrake_input]. Nähere Infos siehe weiter unten.

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

### input_frontend.php
Enthält das HTML und die Verarbeitungslogik für die Eingabe von Events und 
Locations über das Frontend (siehe Shortcode [eventkrake_input]).

##Shortcodes
Zur Zeit gibt es einen Shortcode.

### [eventkrake_input]
Damit wird ein komplexes Eingabefeld geschaffen um Events und Locations
anzulegen.

**Achtung** Die Bearbeitung der angelegten Events und Locations ist darüber
nicht möglich. Dafür gibt es einen Link, über den Änderungen per E-Mail
gemeldet werden können.

Der Shortcode versteht folgende Attribute:

* **author** *int* Die Author-ID, unter der die Posts abgelegt werden. Default ist 1.
* **email** *string* Die E-Mail, an die Änderungsmeldungen geschickt werden. Default ist die im Wordpress hinterlegte Admin-E-Mail-Adresse.
* **startdate** *ISO8601-Datum* Eine Zeitangabe, die den Defaultwert für den Start von Events angibt. Als Standard wird das aktuelle Datum und Uhrzeit verwendet.
* **enddate** *ISO8601-Datum* Eine Zeitangabe, die den Defaultwert für das Ende von Events angibt. Als Standard wird das aktuelle Datum und Uhrzeit verwendet.
* **festival** *string* ID eines Eventkrake-Festivals, auf die die Events und Locations gesetzt werden sollen. Default ist kein Festival.
