<?php
/*
Plugin Name: Eventkrake 3 WP Plugin
Plugin URI: http://eventkrake.de/code/
Description: Eine Veranstaltungsverwaltung, die Veranstaltungen mit Orten samt Geokoordinaten verknüpft. Die Darstellung ist über Templates flexibel anpassbar.
Author: Jan Kossick
Version: 3.0beta
License: CC-BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
Author URI: http://jankossick.de
Min WP Version: 3.7
Text Domain: g4rf_eventkrake2
*/

/***** Needs & needles *****/
setlocale(LC_TIME, get_locale());
add_theme_support('post-thumbnails'); // Bilder anlegen
require_once 'Eventkrake.php';


/***** Session-Funktionalität (CAPTCHA etc.) *****/
add_action('init', function() {
    if(!session_id()) session_start();
}, 1);
add_action('wp_logout', 'session_destroy');
add_action('wp_login', 'session_destroy');


/***** Scripte & CSS hinzufügen *****/
// Backend JS und CSS
add_action('admin_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // Geolokalisation
    wp_register_script('eventkrake_googlemaps',
        'https://maps.google.com/maps/api/js?region=DE&key=AIzaSyClvezOaz9z-nZKjMmYRe0cvvWEiCnWjmE');
    wp_enqueue_script('eventkrake_googlemaps');
    // Leaflet
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        array('jquery'));
    wp_enqueue_script('eventkrake_leaflet');
    // allgemeine Scripte
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        array('eventkrake_leaflet'));
    wp_enqueue_script('eventkrake');
    // Adminscripte
    wp_register_script('eventkrake_admin', $path.'js/admin.js',
        array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'eventkrake'));
    wp_enqueue_script('eventkrake_admin');

    // allgemeines CSS
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // Admin-CSS
    wp_register_style('eventkrake_admin', $path.'css/admin.css',
        array('eventkrake_all'));
    wp_enqueue_style('eventkrake_admin');
    // jQuery-UI
    wp_register_style('eventkrake_jquery-ui',
        'https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
    wp_enqueue_style('eventkrake_jquery-ui');
    // Leaflet CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});

// Frontend JS und CSS
add_action('wp_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // Geolokalisation
    wp_register_script('eventkrake_googlemaps',
        'https://maps.google.com/maps/api/js?region=DE&key=AIzaSyClvezOaz9z-nZKjMmYRe0cvvWEiCnWjmE');
    wp_enqueue_script('eventkrake_googlemaps');
    // Leaflet-JS
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        array('jquery'));
    wp_enqueue_script('eventkrake_leaflet');
    // allgemeines JS
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        array('eventkrake_leaflet'));
    wp_enqueue_script('eventkrake');
    // Input JS
    wp_register_script('eventkrake_input',  $path.'js/input.js',
        array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'eventkrake'));
    wp_enqueue_script('eventkrake_input');
    wp_localize_script('eventkrake_input', 'EventkrakeInputAjax', array(
        'url' => admin_url('admin-ajax.php')
    ));

    // allgemeines CSS
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // Input CSS
    wp_register_style('eventkrake_input', $path.'css/input.css');
    wp_enqueue_style('eventkrake_input');
    // jQuery-UI
    wp_register_style('eventkrake_jquery-ui',
        'https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
    wp_enqueue_style('eventkrake_jquery-ui');
    // Leaflet-CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});



/***** Custom Post Types *****/

/* Locations anlegen */
add_action('init', function () {
    register_post_type('eventkrake_location', array(
        'public' => true,
        'has_archive' => true,
        'labels' => array(
            'name' => __('Orte', 'g4rf_eventkrake2'),
            'singular_name' => __('Ort', 'g4rf_eventkrake2'),
            'add_new' => __('Ort hinzufügen', 'g4rf_eventkrake2'),
            'add_new_item' => __('Neuen Ort hinzufügen', 'g4rf_eventkrake2'),
            'edit' => __('Ort bearbeiten', 'g4rf_eventkrake2'),
            'edit_item' => __('Ort bearbeiten', 'g4rf_eventkrake2'),
            'new_item' => __('Ort hinzufügen', 'g4rf_eventkrake2'),
            'view' => __('Ort anschauen', 'g4rf_eventkrake2'),
            'search_items' => __('Ort suchen', 'g4rf_eventkrake2'),
            'not_found' => __('Keine Orte gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' =>
                __('Keine gelöschten Orte', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'location'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/location.png',
        'description' =>
            __('An Orten finden Veranstaltungen statt.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_location',
                __('Weitere Angaben zum Ort', 'g4rf_eventkrake2'),
                function($args = null) {
                    // Inhalt der Metabox
                    include 'meta_location.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});
// Inhalt der Metabox speichern
add_action('save_post_eventkrake_location', function($post_id, $post) {
    //die("$post_id<pre>" . print_r($post, true) . "</pre>");

    // checken, ob wir vom edit screen kommen
    if(! isset($_POST['eventkrake_on_edit_screen'])) return;

    // automatische Speicherungen synchronisieren wir nicht
    if($post->post_status == 'auto-draft') return;

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return;

    // If this is just a revision, do nothing.
    if (wp_is_post_revision($post_id)) return;

    // check POST-fields
    $name = $_POST['post_title'];
    $text = $_POST['post_content'];
    $lat = $_POST['eventkrake_lat'];
    $lng = $_POST['eventkrake_lng'];
    $address = $_POST['eventkrake_address'];

    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $linksArray = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $linksArray[$linksKeys[$i]] = $linksValues[$i];
    }
    $links = json_encode($linksArray);

    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }
    $tags = $_POST['eventkrake_tags'];

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'lat', $lat);
    Eventkrake::setSinglePostMeta($post_id, 'lng', $lng);
    Eventkrake::setSinglePostMeta($post_id, 'address', $address);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);
}, 1, 2);

/* Veranstaltungen anlegen */
add_action('init', function () {
    register_post_type('eventkrake_event', array(
        'public' => true,
        'has_archive' => true,
        'labels' => array(
            'name' => __('Veranstaltungen', 'g4rf_eventkrake2'),
            'singular_name' => __('Veranstaltung', 'g4rf_eventkrake2'),
            'add_new' => __('Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'add_new_item' => __('Neue Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'edit' => __('Veranstaltung ändern', 'g4rf_eventkrake2'),
            'edit_item' => __('Veranstaltung ändern', 'g4rf_eventkrake2'),
            'new_item' => __('Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'view' => __('Veranstaltung ansehen', 'g4rf_eventkrake2'),
            'search_items' => __('Veranstaltung suchen', 'g4rf_eventkrake2'),
            'not_found' => __('Keine Veranstaltungen gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' => __('Keine gelöschten Veranstaltungen', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'event'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/event.png',
        'description' => __('Veranstaltungen sind zeitlich begrenzte Ereignisse'
                . ' an einem Ort.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_event',
                __('Weitere Angaben zur Veranstaltung', 'g4rf_eventkrake2'),
                function($args = null) {
                    // Inhalt der Metabox
                    include 'meta_event.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});
// Inhalt der Metabox speichern
add_action('save_post_eventkrake_event', function($post_id, $post) {
    //die("$post_id<pre>" . print_r($_POST, true) . "</pre>");

    // checken, ob wir vom edit screen kommen
    if(! isset($_POST['eventkrake_on_edit_screen'])) return;

    // automatische Speicherungen synchronisieren wir nicht
    if($post->post_status == 'auto-draft') return;

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return;

    // If this is just a revision, do nothing.
    if (wp_is_post_revision($post_id)) return;

    // check POST-fields
    $title = $_POST['post_title'];
    $text = $_POST['post_content'];
    $excerpt = $_POST['post_excerpt'];
    $tags = $_POST['eventkrake_tags'];
    $dateStart = $_POST['eventkrake_startdate'] . 'T' .
            $_POST['eventkrake_starthour'] . ':' .
            $_POST['eventkrake_startminute'] . ':00';
    $dateEnd = $_POST['eventkrake_enddate'] . 'T' .
            $_POST['eventkrake_endhour'] . ':' .
            $_POST['eventkrake_endminute'] . ':00';
    $website = empty($_POST['eventkrake_website']) ?
            get_permalink($post_id) : $_POST['eventkrake_website'];
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }
    $festival = isset($_POST['eventkrake_festival']) ?
        $_POST['eventkrake_festival'] : 0;
    // location ID, jeweils Wordpress _und_ Eventkrake
    $locationIdWordpress = 0;
    $locationIdEventkrake = 0;
    if(isset($_POST['eventkrake_locationid_wordpress'])) {
        $locationIdWordpress = $_POST['eventkrake_locationid_wordpress'];
        if($locationIdWordpress != 0) {
            $locationIdEventkrake = Eventkrake::getSinglePostMeta(
                    $locationIdWordpress, 'id');
        }
    }
    // Künstler*innen
    $artists = is_array($_POST['eventkrake_artists']) ?
            $_POST['eventkrake_artists'] : array();
    // delete the 0 value
    if (($key = array_search(0, $artists)) !== false) {
        unset($artists[$key]);
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id,
            'locationid_wordpress', $locationIdWordpress);
    Eventkrake::setSinglePostMeta($post_id,
            'locationid_eventkrake', $locationIdEventkrake);
    Eventkrake::setSinglePostMeta($post_id, 'start', $dateStart);
    Eventkrake::setSinglePostMeta($post_id, 'end', $dateEnd);
    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setSinglePostMeta($post_id, 'website', $website);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);
    Eventkrake::setSinglePostMeta($post_id, 'festival', $festival);
    Eventkrake::setPostMeta($post_id, 'artists', $artists);

}, 1, 2);

/* Künstlerinnen und Künstler */
add_action('init', function () {
    register_post_type('eventkrake_artist', array(
        'public' => true,
        'has_archive' => true,
        'labels' => array(
            'name' => __('KünstlerInnen', 'g4rf_eventkrake2'),
            'singular_name' => __('KünstlerIn', 'g4rf_eventkrake2'),
            'add_new' => __('KünstlerIn hinzufügen', 'g4rf_eventkrake2'),
            'add_new_item' => __('Neue KünstlerIn hinzufügen', 'g4rf_eventkrake2'),
            'edit' => __('KünstlerIn bearbeiten', 'g4rf_eventkrake2'),
            'edit_item' => __('KünstlerIn bearbeiten', 'g4rf_eventkrake2'),
            'new_item' => __('KünstlerIn hinzufügen', 'g4rf_eventkrake2'),
            'view' => __('KünstlerIn ansehen', 'g4rf_eventkrake2'),
            'search_items' => __('KünstlerIn suchen', 'g4rf_eventkrake2'),
            'not_found' => __('Keine KünstlerIn gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' => __('Keine gelöschten KünstlerInnen', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'artist'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/artist.png',
        'description' => __('Künstlerinnen und Künstler sind Einzelpersonen oder'
                . ' Gruppen.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'excerpt', 'revisions', 'editor', 'thumbnail'),
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_artist',
                __('Weitere Angaben', 'g4rf_eventkrake2'),
                function($args = null) {
                    // Inhalt der Metabox
                    include 'meta_artist.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});
// Inhalt der Metabox speichern
add_action('save_post_eventkrake_artist', function($post_id, $post) {
    //die("$post_id<pre>" . print_r($post, true) . "</pre>");

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return;

    // checken, ob wir vom edit screen kommen
    if(! isset($_POST['eventkrake_on_edit_screen'])) return;

    // automatische Speicherungen machen wir nicht
    if($post->post_status == 'auto-draft') return;

    // If this is just a revision, do nothing.
    if (wp_is_post_revision($post_id)) return;

    // check POST-fields
    $artTypes = $_POST['art_types'];

    $linkNames = array(
        $_POST['eventkrake_linknames0'],
        $_POST['eventkrake_linknames1'],
        $_POST['eventkrake_linknames2'],
        $_POST['eventkrake_linknames3'],
        $_POST['eventkrake_linknames4']
    );
    $linkUrls = array(
        $_POST['eventkrake_linkurls0'],
        $_POST['eventkrake_linkurls1'],
        $_POST['eventkrake_linkurls2'],
        $_POST['eventkrake_linkurls3'],
        $_POST['eventkrake_linkurls4']
    );

    $origin = $_POST['eventkrake_origin'];

    $festivals = filter_input(INPUT_POST, 'eventkrake_festivals',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if(! $festivals) $festivals = [];

    // save fields
    Eventkrake::setPostMeta($post_id, 'artTypes', $artTypes);
    Eventkrake::setPostMeta($post_id, 'linknames', $linkNames);
    Eventkrake::setPostMeta($post_id, 'linkurls', $linkUrls);
    Eventkrake::setSinglePostMeta($post_id, 'origin', $origin);
    Eventkrake::setPostMeta($post_id, 'festivals', $festivals);
}, 1, 2);



/***** Shortcode für Ausgabe *****/
add_shortcode('eventkrake', function($atts, $content = null) {
    // put shortcode attributes into DOM as data element
    $dataAtts = '';
    foreach($atts as $k => $a) {
        if(strlen($a) == 0) continue;
        $dataAtts .= " data-$k='$a'";
    }
    ?><div class="Eventkrake"<?=$dataAtts?>></div><?php
});



/***** Frontend-Eingabemaske *****/
// shortcode
add_shortcode('eventkrake_input', function($attributes) {
    ob_start();
    $atts = shortcode_atts(array(
        'author' => 1,
        'startdate' => date('Y-m-dTH:i'),
        'enddate' => date('Y-m-dTH:i'),
        'dateformat' => 'd.m.Y H:i', //gibt nur Datum aus: get_option('date_format', 'd.m.Y H:i'),
        'festival' => '',
        'email' => get_option('admin_email', ''),
        'lat' => '',
        'lng' => '',
        'categories' => ''
    ), $attributes);
    ?><div id="eventkrake-input"><?php
        include('input_frontend.php');
    ?></div><?php
    return ob_get_clean();
});

// ajax function for Eventkrake input
add_action('wp_ajax_EventkrakeInputAjax', 'EventkrakeInputAjax');
add_action('wp_ajax_nopriv_EventkrakeInputAjax', 'EventkrakeInputAjax');
function EventkrakeInputAjax() {
    // check human challenge
    if(! Eventkrake::humanChallenge($_SESSION['challenge'],
            filter_input(INPUT_POST, 'eventkrake-input-response'))) {
        $_SESSION['challenge'] = Eventkrake::humanChallenge();
        EventkrakeExitAjax(400, array(
            'error' => true,
            'captcha' => $_SESSION['challenge'],
            'msg' => __('Bitte gib eine Antwort an um zu prüfen, ob Du menschlich bist.',
                    'g4rf_eventkrake2'),
            'tab' => '[data-me="captcha"]',
            'focus' => '[name="eventkrake-input-response"]'
        ));
    }

    // check e-mail
    if(empty(filter_input(INPUT_POST, 'eventkrake-input-email'))) {
        EventkrakeExitAjax(400, array(
            'error' => true,
            'msg' => __('Gib bitte eine E-Mail-Adresse an.', 'g4rf_eventkrake2'),
            'tab' => '[data-me="captcha"]',
            'focus' => '[name="eventkrake-input-email"]'
        ));
    }

    // selected existing location
    if('list' == filter_input(INPUT_POST, 'eventkrake-input-location-radio')) {
        $locationId = filter_input(INPUT_POST, 'eventkrake-input-locationlist');

    } else { // new location added
        // address or geo coords missing
        if(empty($_POST['eventkrake-lat']) || empty($_POST['eventkrake-lng'])
                || empty($_POST['eventkrake-address'])) {
            EventkrakeExitAjax(400, array(
                'error' => true,
                'msg' => __('Keine Adresse angegeben oder Marker nicht gesetzt.',
                    'g4rf_eventkrake2'),
                'tab' => '[date-me="location"]',
                'focus' => '[name="eventkrake-address"]'
            ));
        }

        // location name missing
        if(empty($_POST['eventkrake-location-name'])) {
            EventkrakeExitAjax(400, array(
                'error' => true,
                'msg' => __('Keinen Namen für den Ort angegeben.',
                    'g4rf_eventkrake2'),
                'tab' => '[date-me="location"]',
                'focus' => '[name="eventkrake-location-name"]'
            ));
        }

        // insert location into database
        $locationId = wp_insert_post(array(
            'post_title' => wp_strip_all_tags(
                    filter_input(INPUT_POST, 'eventkrake-location-name')),
            'post_content' => nl2br(
                    filter_input(INPUT_POST, 'eventkrake-location-text')),
            'post_type' => 'eventkrake_location',
            'post_author' => $atts['author']
        ));
        if($locationId) {
            // lat
            Eventkrake::setSinglePostMeta($locationId,
                    'lat', filter_input(INPUT_POST, 'eventkrake-lat'));
            // lng
            Eventkrake::setSinglePostMeta($locationId,
                    'lng', filter_input(INPUT_POST, 'eventkrake-lng'));
            // address
            Eventkrake::setSinglePostMeta($locationId,
                    'address', filter_input(INPUT_POST, 'eventkrake-address'));
            // website
            Eventkrake::setSinglePostMeta($locationId,
                    'website', filter_input(INPUT_POST, 'eventkrake-location-website'));
            // categories
            $categories = filter_input(INPUT_POST, 'eventkrake_location_categories');
            if($categories) {
                if(! is_array($categories[$i])) {
                    $categories[$i] = explode(",", $categories[$i]);
                    foreach($categories[$i] as &$c) {
                        $c = trim($c);
                    }
                    unset($c);
                }
                Eventkrake::setPostMeta($locationId, 'categories', $categories[$i]);
            }
            // festivals
            if(! empty($_POST['eventkrake-input-festival'])) {
                Eventkrake::setPostMeta($locationId, 'festivals',
                        array(filter_input(INPUT_POST, 'eventkrake-input-festival')));
            }
            // tags
            Eventkrake::setSinglePostMeta($locationId,
                    'tags', filter_input(INPUT_POST, 'eventkrake-input-email'));
        }
    }

    // add events
    $startDates = filter_input(INPUT_POST, 'eventkrake-startdate',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $startHours = filter_input(INPUT_POST, 'eventkrake-starthour',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $startMinutes = filter_input(INPUT_POST, 'eventkrake-startminute',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $lengthHours = filter_input(INPUT_POST, 'eventkrake-lengthhour',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $lengthMinutes = filter_input(INPUT_POST, 'eventkrake-lengthminute',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $titles = filter_input(INPUT_POST, 'eventkrake-event-title',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $texts = filter_input(INPUT_POST, 'eventkrake-event-text',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $websites = filter_input(INPUT_POST, 'eventkrake-event-website',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $categories = filter_input(INPUT_POST, 'eventkrake-event-category',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    // ! index 0 is the row template and must not be used
    for($i = 1; $i < count($titles); $i++) {
        // no title, no event
        if(empty($titles[$i])) continue;

        $eventId = wp_insert_post(array(
            'post_title' => wp_strip_all_tags($titles[$i]),
            'post_content' => nl2br($texts[$i]),
            'post_type' => 'eventkrake_event',
            'post_author' => $atts['author']
        ));
        if($eventId) {
            // location id
            Eventkrake::setSinglePostMeta($eventId,
                    'locationid_wordpress', $locationId);
            // start
            $start = new DateTime($startDates[$i] . ' ' .
                    $startHours[$i] . ':' . $startMinutes[$i] . ':00');
            Eventkrake::setSinglePostMeta($eventId, 'start', $start->format('c'));
            // end
            $end = $start->add(
                new DateInterval("PT{$lengthHours[$i]}H{$lengthMinutes[$i]}M")
            );
            Eventkrake::setSinglePostMeta($eventId, 'end', $end->format('c'));
            // website
            if(strlen($websites[$i]) > 0)
                Eventkrake::setSinglePostMeta($eventId, 'website', $websites[$i]);
            // categories
            if(isset($categories[$i])) {
                if(! is_array($categories[$i])) {
                    $categories[$i] = explode(",", $categories[$i]);
                    foreach($categories[$i] as &$c) {
                        $c = trim($c);
                    }
                    unset($c);
                }
                Eventkrake::setPostMeta($eventId, 'categories', $categories[$i]);
            }
            // festival
            if(! empty($_POST['eventkrake-input-festival'])) {
                Eventkrake::setSinglePostMeta($eventId, 'festival',
                        filter_input(INPUT_POST, 'eventkrake-input-festival'));
            }
            // tags
            Eventkrake::setSinglePostMeta($eventId,
                'tags', filter_input(INPUT_POST, 'eventkrake-input-email'));
        }
    }

    // all done
    EventkrakeExitAjax(200, array('locationId' => $locationId));
}

function EventkrakeExitAjax($code, $data) {
    status_header($code);
    header( "Content-Type: application/json" );
    print json_encode($data);
	wp_die();
}