<?php
/*
Plugin Name: Eventkrake 3 WP Plugin
Plugin URI: http://eventkrake.de/code/
Description: Eine Veranstaltungsverwaltung, die Veranstaltungen mit Orten samt Geokoordinaten verknüpft. Die Darstellung ist über Templates flexibel anpassbar.
Author: Jan Kossick
Version: 3.0beta
License: CC-BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
Author URI: http://jankossick.de
Min WP Version: 5.3
Text Domain: g4rf_eventkrake2
*/

/***** Needs & needles *****/
setlocale(LC_TIME, get_locale());
add_theme_support('post-thumbnails');
require_once 'Eventkrake.php';


/***** convert from 2 to 3 *****/

// copy locationid_wordpress to locationid
/*$locationIds = $wpdb->get_results($wpdb->prepare(
    "SELECT post_id,meta_value FROM $wpdb->postmeta WHERE meta_key = %s",
        'eventkrake_locationid_wordpress'));
foreach($locationIds as $l) {
    $wpdb->insert($wpdb->postmeta, [
        'post_id' => $l->post_id,
        'meta_value' => $l->meta_value,
        'meta_key' => 'eventkrake_locationid'
    ]);
}*/
//print "<pre>"; print_r($locationIds); die();


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

/* LOCATIONS */
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
    $lat = $_POST['eventkrake_lat'];
    $lng = $_POST['eventkrake_lng'];
    $address = $_POST['eventkrake_address'];
    $tags = $_POST['eventkrake_tags'];

    // links
    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[$linksKeys[$i]] = $linksValues[$i];
    }

    // categories
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'lat', $lat);
    Eventkrake::setSinglePostMeta($post_id, 'lng', $lng);
    Eventkrake::setSinglePostMeta($post_id, 'address', $address);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);
}, 1, 2);


/* EVENTS */
add_action('init', function () {
    register_post_type('eventkrake_event', array(
        'public' => true,
        'has_archive' => true,
        'labels' => array(
            'name' => __('Veranstaltungen', 'g4rf_eventkrake2'),
            'singular_name' => __('Veranstaltung', 'g4rf_eventkrake2'),
            'add_new' => __('Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'add_new_item' =>
                __('Neue Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'edit' => __('Veranstaltung ändern', 'g4rf_eventkrake2'),
            'edit_item' => __('Veranstaltung ändern', 'g4rf_eventkrake2'),
            'new_item' => __('Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'view' => __('Veranstaltung ansehen', 'g4rf_eventkrake2'),
            'search_items' => __('Veranstaltung suchen', 'g4rf_eventkrake2'),
            'not_found' =>
                __('Keine Veranstaltungen gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' =>
                __('Keine gelöschten Veranstaltungen', 'g4rf_eventkrake2')
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

    // check POST fields
    $tags = $_POST['eventkrake_tags'];

    // times
    for($i = 1; $i < count($_POST['eventkrake_startdate']); $i++) {
        $datesStart[] = $_POST['eventkrake_startdate'][$i] . 'T' .
            $_POST['eventkrake_starthour'][$i] . ':' .
            $_POST['eventkrake_startminute'][$i] . ':00';
        $datesEnd[] = $_POST['eventkrake_enddate'][$i] . 'T' .
            $_POST['eventkrake_endhour'][$i] . ':' .
            $_POST['eventkrake_endminute'][$i] . ':00';
    }

    // links
    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[$linksKeys[$i]] = $linksValues[$i];
    }

    // categories
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // location id
    $locationId = isset($_POST['eventkrake_locationid']) ?
        $_POST['eventkrake_locationid'] : 0;

    // artists
    $artists = is_array($_POST['eventkrake_artists']) ?
            $_POST['eventkrake_artists'] : array();
    // delete the 0 value
    if (($key = array_search(0, $artists)) !== false) {
        unset($artists[$key]);
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'locationid', $locationId);

    Eventkrake::setPostMeta($post_id, 'start', $datesStart);
    Eventkrake::setPostMeta($post_id, 'end', $datesEnd);

    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);

    Eventkrake::setPostMeta($post_id, 'artists', $artists);

}, 1, 2);


/* ARTISTS */
add_action('init', function () {
    register_post_type('eventkrake_artist', array(
        'public' => true,
        'has_archive' => true,
        'labels' => array(
            'name' => __('Künstler:innen', 'g4rf_eventkrake2'),
            'singular_name' => __('Künstler:in', 'g4rf_eventkrake2'),
            'add_new' => __('Künstler:in hinzufügen', 'g4rf_eventkrake2'),
            'add_new_item' =>
                    __('Neue Künstler:in hinzufügen', 'g4rf_eventkrake2'),
            'edit' => __('Künstler:in bearbeiten', 'g4rf_eventkrake2'),
            'edit_item' => __('Künstler:in bearbeiten', 'g4rf_eventkrake2'),
            'new_item' => __('Künstler:in hinzufügen', 'g4rf_eventkrake2'),
            'view' => __('Künstler:in ansehen', 'g4rf_eventkrake2'),
            'search_items' => __('Künstler:in suchen', 'g4rf_eventkrake2'),
            'not_found' => __('Keine Künstler:in gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' =>
                    __('Keine gelöschten Künstler:innen', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'artist'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/artist.png',
        'description' =>
                __('Künstler:innen sind Einzelpersonen oder
                         Gruppen.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
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

    // check POST fields
    $tags = $_POST['eventkrake_tags'];

    // categories
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // links
    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[$linksKeys[$i]] = $linksValues[$i];
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
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
            Eventkrake::setSinglePostMeta($eventId, 'locationid', $locationId);
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



/***** REST API for events, locations and artists *****/

function eventkrake_restbuild_artist($artist) {
    $id = $artist->ID;
    return [
        'id' => $id,
        'name' => $artist->post_title,
        'text' => apply_filters('the_content', $artist->post_content),
        'image' =>  get_the_post_thumbnail_url($id, 'full'),
        'categories' => Eventkrake::getPostMeta($id, 'categories'),
        'links' => Eventkrake::getSinglePostMeta($id, 'links'),
        'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
    ];
}
function eventkrake_restbuild_location($location) {
    $id = $location->ID;
    return [
        'id' => $id,
        'name' => $location->post_title,
        'address' =>
            Eventkrake::getSinglePostMeta($id, 'address'),
        'lat' => Eventkrake::getSinglePostMeta($id, 'lat'),
        'lng' => Eventkrake::getSinglePostMeta($id, 'lng'),
        'text' => apply_filters('the_content',
                                    $location->post_content),
        'image' =>  get_the_post_thumbnail_url($id, 'full'),
        'categories' => Eventkrake::getPostMeta($id, 'categories'),
        'links' => Eventkrake::getSinglePostMeta($id, 'links'),
        'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
    ];
}
function eventkrake_restbuild_event($event, $params = []) {
    $id = $event->ID;
    $events = [];
    $startDates = Eventkrake::getPostMeta($id, 'start');
    $endDates = Eventkrake::getPostMeta($id, 'end');

    // params
    $earliestStart = false;
    if(isset($params['earliestStart'])) {
        try {
            $earliestStart = new DateTime($params['earliestStart']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter earliestStart is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }
    $earliestEnd = false;
    if(isset($params['earliestEnd'])) {
        try {
            $earliestEnd = new DateTime($params['earliestEnd']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter earliestEnd is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }
    $latestStart = false;
    if(isset($params['latestStart'])) {
        try {
            $latestStart = new DateTime($params['latestStart']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter latestStart is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }
    $latestEnd = false;
    if(isset($params['latestEnd'])) {
        try {
            $latestEnd = new DateTime($params['latestEnd']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter latestEnd is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }

    // go through dates
    for($i = 0; $i < count($startDates); $i++) {
        // check dates
        $eventStart = new DateTime($startDates[$i]);
        $eventEnd = new DateTime($endDates[$i]);
        if($earliestStart != false && $eventStart < $earliestStart) continue;
        if($earliestEnd != false && $eventEnd < $earliestEnd) continue;
        if($latestStart != false && $eventStart > $latestStart) continue;
        if($latestEnd != false && $eventEnd > $latestEnd) continue;

        // add event
        $events[] = [
            'id' => $id,
            'name' => $event->post_title,
            'text' => apply_filters('the_content',
                                        $event->post_content),
            'image' =>  get_the_post_thumbnail_url($id, 'full'),
            'locationid' => Eventkrake::getSinglePostMeta($id, 'locationid'),
            'start' => $startDates[$i],
            'end' => $endDates[$i],
            'artists' => Eventkrake::getPostMeta($id, 'artists'),
            'categories' => Eventkrake::getPostMeta($id, 'categories'),
            'links' => Eventkrake::getSinglePostMeta($id, 'links'),
            'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
        ];
    }

    return $events;
}

// sort events for date ASC
function eventkrake_sortevents($a, $b) {
    $aDate = new DateTime($a['start']);
    $bDate = new DateTime($b['start']);
    if($aDate < $bDate) return -1;
    if($aDate > $bDate) return 1;
    return 0;
}

// ROUTES
function eventkrake_register_routes() {
    $base = 'eventkrake/v3';

    // GET locations
    register_rest_route($base, '/locations', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => function() {
            $locations = [];
            $events = [];
            $artists = [];
            foreach(Eventkrake::getLocations() as $location) {
                $locations[$location->ID] = eventkrake_restbuild_location($location);

                // events
                foreach(Eventkrake::getEvents($location->ID) as $event) {
                    $events = array_merge($events, eventkrake_restbuild_event($event));

                    // artists
                    foreach(Eventkrake::getPostMeta($event->ID, 'artists') as $artistId) {
                        if(! array_key_exists($artistId, $artists)) {
                            $artists[$artistId] =
                                    eventkrake_restbuild_artist(get_post($artistId));
                        }
                    }
                }
            }

            // sort events
            usort($events, 'eventkrake_sortevents');

            return rest_ensure_response([
                'locations' => $locations,
                'events' => $events,
                'artists' => $artists
            ]);
        }
    ]);

    // GET events
    register_rest_route($base, '/events', [
        'methods'  => WP_REST_Server::READABLE,
        'args' => [
            'earliestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal date for the events. This parameter is checked against the start of an event.', 'g4rf_eventkrake2')
            ],
            'earliestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal date for the events. This parameter is checked against the end of an event.', 'g4rf_eventkrake2')
            ],
            'latestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal date for the events. This parameter is checked against the start of an event.', 'g4rf_eventkrake2')
            ],
            'latestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal date for the events. This parameter is checked against the end of an event.', 'g4rf_eventkrake2')
            ]
        ],
        'callback' => function($params) {
            $events = [];
            $locations = [];
            $artists = [];
            foreach(Eventkrake::getAllEvents() as $event) {
                $filteredEvents = eventkrake_restbuild_event($event, $params);
                if(count($filteredEvents) < 1) continue;

                $events = array_merge($events, $filteredEvents);

                // location
                $locationId = Eventkrake::getSinglePostMeta($event->ID, 'locationid');
                if(! array_key_exists($locationId, $locations)) {
                    if(!$location = get_post($locationId)) continue;
                    $locations[$locationId] =
                            eventkrake_restbuild_location($location);
                }

                // artists
                foreach(Eventkrake::getPostMeta($event->ID, 'artists') as $artistId) {
                    if(! array_key_exists($artistId, $artists)) {
                        $artists[$artistId] =
                                eventkrake_restbuild_artist(get_post($artistId));
                    }
                }
            }

            // sort events
            usort($events, 'eventkrake_sortevents');

            return rest_ensure_response([
                'events' => $events,
                'locations' => $locations,
                'artists' => $artists
            ]);
        }
    ]);

    // GET artists
    register_rest_route($base, '/artists', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => function() {
            $events = []; $eventsCollection = [];
            $locations = [];
            $artists = [];
            foreach(Eventkrake::getArtists() as $artist) {
                $artists[$artist->ID] = eventkrake_restbuild_artist($artist);

                foreach(Eventkrake::getEventsForArtist($artist->ID) as $event) {
                    // ! only collect events
                    $eventsCollection[$event->ID] = $event;

                    // location
                    $locationId = Eventkrake::getSinglePostMeta($event->ID, 'locationid');
                    if(! array_key_exists($locationId, $locations)) {
                        if(!$location = get_post($locationId)) continue;
                        $locations[$locationId] =
                                eventkrake_restbuild_location($location);
                    }
                }
            }

            // process events
            foreach($eventsCollection as $event) {
                $events = array_merge($events, eventkrake_restbuild_event($event));
            }
            // sort events
            usort($events, 'eventkrake_sortevents');

            return rest_ensure_response([
                'events' => $events,
                'locations' => $locations,
                'artists' => $artists
            ]);
        }
    ]);
}
add_action('rest_api_init', 'eventkrake_register_routes');