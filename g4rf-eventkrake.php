<?php
/**
 * Plugin Name:     Eventkrake
 * Plugin URI:      https://github.com/g4rf/g4rf-eventkrake
 * Description:     A wordpress plugin to manage events, locations and artists. It has an REST endpoint to use the data in external applications.
 * Author:          Jan Kossick
 * Version:         5.10beta
 * License:         CC BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
 * Author URI:      https://jankossick.de
 * Min WP Version:  6.5
 * Text Domain:     eventkrake
 * Domain Path:     /lang
 * 
 * @package         g4rf
 */

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/*
 * Needs & needles 
 */

// featured images
add_theme_support('post-thumbnails');

// translations
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'eventkrake', false, 
        dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
});

// classes and helper
require_once 'Eventkrake/Type/Link.php';
require_once 'Eventkrake/Config.php';

require_once 'Eventkrake/Eventkrake.php';
require_once 'Eventkrake/Event.php';
require_once 'Eventkrake/Artist.php';
require_once 'Eventkrake/Location.php';

require_once 'Eventkrake/Widget/ShowNextEvents.php';

use Eventkrake\Config as Config;
use Eventkrake\Eventkrake as Eventkrake;
use Eventkrake\Event as Event;
use Eventkrake\Location as Location;
use Eventkrake\Artist as Artist;


/*
 * Installation
 */

register_activation_hook( __FILE__, function() {
    // look if category `eventkrake` is already set
    if(term_exists('eventkrake', 'category') != null) return;
    
    // category: eventkrake
    $id = wp_insert_category([
        'cat_name' => 'Eventkrake',
        'category_description' => 
            __('Holds the main categories for events, locations and artists.', 'eventkrake'),
        'category_nicename' => 'eventkrake'
    ]);
    if(empty($id)) return;
    foreach(Config::getDefaultWordpressCategories() as $slug => $name) {
        wp_insert_category([
            'cat_name' => $name,
            'category_nicename' => $slug,
            'category_parent' => $id
        ]);
    }
});


/*
 * Widgets 
 */

add_action('widgets_init', function() {
    register_widget('Eventkrake\Widget\ShowNextEvents');
});


/*
 * Scripts & CSS 
 */

// backend
add_action('admin_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // leaflet
    wp_register_script('eventkrake-leaflet-js',  $path . 'leaflet/leaflet.js',
        ['jquery']);
    wp_enqueue_script('eventkrake-leaflet-js');
    // general scripts
    wp_register_script('eventkrake-js',  $path . 'js/plugin.js',
        ['eventkrake-leaflet-js']);
    wp_enqueue_script('eventkrake-js');
    // admin scripts
    wp_register_script('eventkrake-admin-js', $path . 'js/admin.js',
        ['jquery', 'eventkrake-js']);
    wp_enqueue_script('eventkrake-admin-js');

    // admin css
    wp_register_style('eventkrake-admin-css', $path . 'css/admin.css');
    wp_enqueue_style('eventkrake-admin-css');
    // leaflet CSS
    wp_register_style('eventkrake-leaflet-css', $path . 'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake-leaflet-css');
});

// frontend
add_action('wp_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // leaflet
    wp_register_script('eventkrake-leaflet-js',  $path . 'leaflet/leaflet.js',
        ['jquery']);
    wp_enqueue_script('eventkrake-leaflet-js');
    // general scripts
    wp_register_script('eventkrake-js',  $path . 'js/plugin.js',
        ['eventkrake-leaflet-js']);
    wp_enqueue_script('eventkrake-js');

    // frontend css
    wp_register_style('eventkrake-frontend-css', $path . 'css/frontend.css');
    wp_enqueue_style('eventkrake-frontend-css');
    // leaflet css
    wp_register_style('eventkrake-leaflet-css', $path . 'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake-leaflet-css');
});


/*
 * Blocks 
 */

add_action( 'init', function() {
    // events list
    register_block_type( __DIR__ . '/blocks/build/events-list' );
});


/*
 * shortcodes
 */

add_shortcode('eventkrake', function($atts, $content = null) {
    // put shortcode attributes into DOM as data element
    $dataAtts = '';
    foreach($atts as $k => $a) {
        if(strlen($a) == 0) continue;
        $dataAtts .= " data-$k='$a'";
    }
    ?><div class="eventkrake-data"<?=$dataAtts?>></div><?php
});


/*
 * settings page
 */

if (is_admin()) {
    // add menu
    add_action('admin_menu', function() {
        add_options_page(
            __('Eventkrake Settings', 'eventkrake'), // option page title
            __('Eventkrake', 'eventkrake'), // menu name
            'manage_options', // capability
            'eventkrake-settings', function() {
                include('settings/settings.php');
            });
    });
    
    // add options
    add_action('admin_init', function() {
        register_setting(
            'eventkrake-settings', 'eventkrake-settings',
            function($data) // validate options
            {                
                // if checkboxes are unchecked, $data is null
                if(empty($data)) $data = [];
            
                // if no settings saved so far, $settings is null
                $settings = get_option('eventkrake-settings');
                if(empty($settings)) $settings = [];
                
                // event meta
                if(empty($data['hide-event-meta'])) {
                    $settings['hide-event-meta'] = false;
                } else {
                    $settings['hide-event-meta'] = true;
                }
                
                // location meta
                if(empty($data['hide-location-meta'])) {
                    $settings['hide-location-meta'] = false;
                } else {
                    $settings['hide-location-meta'] = true;
                }
                
                // artist meta
                if(empty($data['hide-artist-meta'])) {
                    $settings['hide-artist-meta'] = false;
                } else {
                    $settings['hide-artist-meta'] = true;
                }
                
                return $settings;
            }
        );
        
        add_settings_section(
            'eventkrake-main', // unique id for the section
            __('Settings', 'eventkrake'),
            function() {
                // display the purpose of the section
                ?><p><?=
                    __('Settings for displaying events, artists and locations.',
                        'eventkrake')
                ?><?php
            }, 
            'eventkrake' // has to match the do_settings_sections
        );
            
        // hide event meta
        add_settings_field(
            'eventkrake-option-hide-event-meta', // unique id
            __('Hide event meta', 'eventkrake'), // title
            function() { // display the setting
                // has to match the second parameter of register_setting
                $options = get_option('eventkrake-settings');
                ?><label>
                    <input id="eventkrake-option-hide-event-meta"
                           name="eventkrake-settings[hide-event-meta]"
                           type="checkbox"<?php
                        if($options['hide-event-meta'] == true) {
                            ?> checked <?php
                        }
                    ?>><?=__('Don\'t show infos on event pages.', 
                        'eventkrake')
                ?></label><?php
            }, 
            'eventkrake', // same as do_settings_section
            'eventkrake-main' // same as add_settings_section
        );
            
        // hide location meta
        add_settings_field(
            'eventkrake-option-hide-location-meta', // unique id
            __('Hide location meta', 'eventkrake'), // title
            function() { // display the setting
                // has to match the second parameter of register_setting
                $options = get_option('eventkrake-settings');
                ?><label>
                    <input id="eventkrake-option-hide-location-meta"
                           name="eventkrake-settings[hide-location-meta]"
                           type="checkbox"<?php
                        if($options['hide-location-meta'] == true) {
                            ?> checked <?php
                        }
                    ?>><?=__('Don\'t show infos on location pages.', 
                        'eventkrake')
                ?></label><?php
            }, 
            'eventkrake', // same as do_settings_section
            'eventkrake-main' // same as add_settings_section
        );
            
        // hide artist meta
        add_settings_field(
            'eventkrake-option-hide-artist-meta', // unique id
            __('Hide artist meta', 'eventkrake'), // title
            function() { // display the setting
                // has to match the second parameter of register_setting
                $options = get_option('eventkrake-settings');
                ?><label>
                    <input id="eventkrake-option-hide-artist-meta"
                           name="eventkrake-settings[hide-artist-meta]"
                           type="checkbox"<?php
                        if($options['hide-artist-meta'] == true) {
                            ?> checked <?php
                        }
                    ?>><?=__('Don\'t show infos on artist pages.', 
                        'eventkrake')
                ?></label><?php
            }, 
            'eventkrake', // same as do_settings_section
            'eventkrake-main' // same as add_settings_section
        );
    });
}

/*
 * REST API for events, locations and artists
 */

function eventkrake_restbuild_artist($artist) {
    return [
        'id' => $artist->ID,
        'url' => $artist->getPermalink(),
        'name' => $artist->getTitle(),
        'title' => $artist->getTitle(),
        'text' => $artist->getContent(),
        'content' => $artist->getContent(),
        'excerpt' => $artist->getExcerpt(),
        'image' =>  get_the_post_thumbnail_url($artist->ID, 'full'),
        'categories' => $artist->getCategories(),
        'wpcategories' => $artist->getWordpressCategories(),
        'wptags' => $artist->getWordpressTags(),
        'links' => $artist->getLinks()
    ];
}
function eventkrake_restbuild_location($location) {
    return [
        'id' => $location->ID,
        'url' => $location->getPermalink(),
        'name' => $location->getTitle(),
        'title' => $location->getTitle(),
        'address' => $location->getAddress(),
        'lat' => $location->getLat(),
        'lng' => $location->getLng(),
        'text' => $location->getContent(),
        'content' => $location->getContent(),
        'excerpt' => $location->getExcerpt(),
        'image' =>  get_the_post_thumbnail_url($location->ID, 'full'),
        'categories' => $location->getCategories(),
        'wpcategories' => $location->getWordpressCategories(),
        'wptags' => $location->getWordpressTags(),
        'links' => $location->getLinks()
    ];
}
function eventkrake_restbuild_event($event) {
    $dateFormat = 'Y-m-d\TH:i:s';
    return [
        'id' => $event->ID,
        'uid' => $event->getUID(),
        'url' => $event->getPermalink(),
        'name' => $event->getTitle(),
        'title' => $event->getTitle(),
        'text' => $event->getContent(),
        'content' => $event->getContent(),
        'excerpt' => $event->getExcerpt(),
        'image' =>  get_the_post_thumbnail_url($event->ID, 'full'),
        'locationid' => $event->getLocationId(),
        'locationId' => $event->getLocationId(),
        'start' => $event->getStart()->format($dateFormat),
        'end' => $event->getEnd()->format($dateFormat),
        'artists' => $event->getArtistIds(),
        'categories' => $event->getCategories(),
        'wpcategories' => $event->getWordpressCategories(),
        'wptags' => $event->getWordpressTags(),
        'links' => $event->getLinks(),
        'icsUrl' => get_site_url(null, '/' . $event->icsParameter(), 'https')
    ];
}

// sort events by date ASC
function eventkrake_sort_rest_events($a, $b) {
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
        'permission_callback' => '__return_true',
        'callback' => function()
        {
            $locations = [];
            $events = [];
            $artists = [];
            foreach(Location::all() as $location) {
                $locations[$location->ID] = 
                    eventkrake_restbuild_location($location);

                // events
                foreach($location->getEvents() as $event) {
                    $events[$event->getUID()] = 
                                eventkrake_restbuild_event($event);

                    // artists
                    foreach($event->getArtistIds() as $artistId) {
                        if(! array_key_exists($artistId, $artists)) {
                            $artists[$artistId] = 
                                eventkrake_restbuild_artist(new Artist($artistId));
                        }
                    }
                }
            }

            // sort events
            usort($events, 'eventkrake_sort_rest_events');

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
        'permission_callback' => '__return_true',
        'args' => [
            'earliestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal start date for the events. This '
                    . 'parameter is checked against the start of an event.', 
                    'eventkrake')
            ],
            'earliestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal end date for the events. This '
                    . 'parameter is checked against the end of an event.', 
                    'eventkrake')
            ],
            'latestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal start date for the events. This '
                    . 'parameter is checked against the start of an event.',
                    'eventkrake')
            ],
            'latestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal end date for the events. This '
                    . 'parameter is checked against the end of an event.', 
                    'eventkrake')
            ]
        ],
        'callback' => function($params) 
        {
            // check params
            $earliestStart = false;
            if(isset($params['earliestStart'])) {
                try {
                    $earliestStart = new DateTime($params['earliestStart']);
                } catch (Exception $ex) {
                    return new WP_Error(
                        'rest_invalid_param',
                        __('The parameter earliestStart is invalid.', 'eventkrake'),
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
                        __('The parameter earliestEnd is invalid.', 'eventkrake'),
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
                        __('The parameter latestStart is invalid.', 'eventkrake'),
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
                        __('The parameter latestEnd is invalid.', 'eventkrake'),
                        ['status' => 400]);
                }
            }
            
            $events = [];
            $locations = [];
            $artists = [];
            foreach(Event::all() as $event) 
            {
                if($earliestStart != false && $event->getStart() < $earliestStart)
                    continue;
                if($earliestEnd != false && $event->getEnd() < $earliestEnd) 
                    continue;
                if($latestStart != false && $event->getStart() > $latestStart) 
                    continue;
                if($latestEnd != false && $event->getEnd() > $latestEnd) 
                    continue;
                
                $events[$event->getUID()] = eventkrake_restbuild_event($event);

                // location
                $locationId = $event->getLocationId();
                if(! array_key_exists($locationId, $locations)) {
                    $locations[$locationId] =
                        eventkrake_restbuild_location(new Location($locationId));
                }

                // artists
                foreach($event->getArtistIds() as $artistId) {
                    if(! array_key_exists($artistId, $artists)) {
                        $artists[$artistId] = 
                            eventkrake_restbuild_artist(new Artist($artistId));
                    }
                }
            }

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
        'permission_callback' => '__return_true',
        'callback' => function() {
            $events = [];
            $locations = [];
            $artists = [];
            foreach(Artist::all() as $artist) {
                $artists[$artist->ID] = eventkrake_restbuild_artist($artist);

                foreach($artist->getEvents() as $event) {
                    $events[$event->getUID()] = 
                        eventkrake_restbuild_event($event);

                    // location
                    $locationId = $event->getLocationId();
                    if(! array_key_exists($locationId, $locations)) {
                        $locations[$locationId] =
                            eventkrake_restbuild_location(new Location($locationId));
                    }
                }
            }

            // sort events
            usort($events, 'eventkrake_sort_rest_events');

            return rest_ensure_response([
                'events' => $events,
                'locations' => $locations,
                'artists' => $artists
            ]);
        }
    ]);
}
add_action('rest_api_init', 'eventkrake_register_routes');
