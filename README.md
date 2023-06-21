# Eventkrake WP Plugin
A wordpress plugin for maintaining events, locations and artists with a REST
endpoint.

* author: Jan Kossick <admin@g4rf.net>
* doc: https://www.eventkrake.de

In its present form, the plugin only extends the admin area and adds three
[Custom Post Types](https://codex.wordpress.org/Post_Types).

The theme is responsible for the presentation in the frontend, which receives 
the data via the [REST API](http://www.eventkrake.de/api/) or directly by 
calling the Eventkrake classes.


## Structure
The structure follows that of a [wordpress plugin](https://codex.wordpress.org/Writing_a_Plugin).
The files must be placed in the folder `/wp-content/plugins/g4rf-eventkrake`.

### g4rf-eventkrake.php
The starting point for Wordpress with meta infos. This is where the required 
scripts are loaded, [Custom Post Types](https://codex.wordpress.org/Post_Types) 
are specified, the callbacks to save the meta info for the custom post types and 
a rudimentary [shortcode](https://codex.wordpress.org/Shortcode_API) is created. 
In addition the routes and endpoints for the REST API are created here.

### meta_event.php
Contains the HTML for the metabox in the admin area "Events".

### meta_location.php
Contains the HTML for the metabox in the "Locations" admin area.

### meta_artists.php
Contains the HTML for the metabox in the admin area "Artists".


## Classes

### \Eventkrake\Eventkrake
The static class `Eventkrake` contains static auxiliary functions for setting 
of post meta-infos, for the output of admin messages and other auxiliary 
functions.

### \Eventkrake\Event

Represents individual events. To create them, the static function
`Event::Factory($id)` should be used, as this creates one event per date and 
returns all events as an array.

### \Eventkrake\Location

Creates a location object.

### \EventKrake\Artist

Creates an Artist object.


## Shortcodes
There is currently one shortcode.

### [eventkrake]
Loads a number of events into the DOM as data attributes. Should only be used 
if a REST retrieval of the data is not possible.