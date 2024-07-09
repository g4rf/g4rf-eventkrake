# Eventkrake WP Plugin
A wordpress plugin for maintaining events, locations and artists with a REST
endpoint.

* author: Jan Kossick <admin@g4rf.net>
* doc: https://www.eventkrake.de

In its present form, the plugin only extends the admin area and adds three
[Custom Post Types](https://codex.wordpress.org/Post_Types). Every post type 
gets a hook injection at `the_content` to add meta infos like location, dates,
participating artists and so on.

It's also possible, that the theme is using the data via the 
[REST API](http://www.eventkrake.de/api/) or directly by calling the Eventkrake 
classes.


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

Represents individual events and the additional info display and the admin meta
box generation.

To create single Events use the static function `Event::Factory($id)` as this 
creates one event per date and returns all events as an array.

### \Eventkrake\Location

Creates a location object and the additional info display and the admin meta
box generation.

### \EventKrake\Artist

Creates an Artist object and the additional info display and the admin meta
box generation.


## Block Editor blocks

### Eventkrake Events List

Shows an events list. Offers several settings like date range and display of
title, content and image.


## Shortcodes
There is currently one shortcode.

### [eventkrake]
Loads a number of events into the DOM as data attributes. Should only be used 
if a REST retrieval of the data is not possible.