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

### input_frontend.php
Enthält das HTML und die Verarbeitungslogik für die Eingabe von Events und
Locations über das Frontend (siehe Shortcode [eventkrake_input]).

## Shortcodes
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
* **lat** *float* Eine Längengradangabe, um den Standardort auf der Karte zu ändern.
* **lng** *float* Eine Breitengradangabe, um den Standardort auf der Karte zu ändern.

**Beispiel**

[eventkrake_input author="2" startdate="2016-06-17T15:00:00" enddate="2016-06-17T15:00:00" email="spam@eventkrake.de"]
