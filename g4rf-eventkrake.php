<?php
/**
 * Plugin Name:     Eventkrake
 * Plugin URI:      https://github.com/g4rf/g4rf-eventkrake
 * Description:     A wordpress plugin to manage events, locations and artists. It has an REST endpoint to use the data in external applications.
 * Author:          Jan Kossick
 * Version:         5.02beta
 * License:         CC BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
 * Author URI:      https://jankossick.de
 * Min WP Version:  6.1
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



/*** LINEUPR ***/

// artists
add_shortcode('lineupr-import-artists', function() {
    ob_start();

    // if not loggedin go out
    if(_wp_get_current_user()->user_login != 'jan') {
        print 'user jan has to be logged in<br />';
        return ob_get_clean();
    }

    // stop manually
    if(true) {
        print 'function manually stopped<br />';
        return ob_get_clean();
    }

    if (! function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (! function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }
    if (! function_exists('wp_read_image_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $lineupr = json_decode(file_get_contents(
        'https://neustadt-leben.lineupr.com/api/organizers/neustadt-leben/events/brn19/data'
    ));

    // import artists
    foreach($lineupr->contributors as $artist) {
        set_time_limit(0);

        // check lineupr-id of existing artists
        foreach(Eventkrake::getArtists(false) as $a) {
            $tags = Eventkrake::getSinglePostMeta($a->ID, 'tags');
            if(strpos($tags, $artist->_id) > 0) continue 2;
        }

        // insert artist
        $description = '&nbsp;';
        if(! empty($artist->subtitle)) {
            if(! empty($artist->descriptionHtml)) {
                $description = "{$artist->subtitle} - {$artist->descriptionHtml}";
            } else {
                $description = $artist->subtitle;
            }
        } elseif(! empty($artist->descriptionHtml)) {
            $description = $artist->descriptionHtml;
        }

        $id = wp_insert_post([
            'post_author'           => get_current_user_id(),
            'post_content'          => $description,
            'post_title'            => wp_strip_all_tags($artist->name),
            'post_status'           => 'publish',
            'post_type'             => 'eventkrake_artist',
            'post_name'             => $artist->alias
        ]);
        if($id == 0) continue;

        print 'adding ' . wp_strip_all_tags($artist->name) . '<br />';

        // tags
        Eventkrake::setSinglePostMeta($id, 'tags', "lineupr-id:{$artist->_id}");

        // categories
        $categories = [];
        foreach($artist->categories as $category) {
            foreach($lineupr->categories as $c) {
                if($c->_id == $category) {
                    $categories[] = $c->name;
                }
            }
        }
        Eventkrake::setPostMeta($id, 'categories', $categories);

        // links
        $links = [];
        foreach($artist->attachments as $a) {
            $links[] = [
                'name' => $a->name,
                'url' => $a->link
            ];
        }
        Eventkrake::setSinglePostMeta($id, 'links', $links);

        // image
        if(isset($artist->teaser) && isset($artist->teaser->original)) {
            $url = 'https://lineupr.com' . $artist->teaser->original;
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $tmp = download_url($url);

            if (is_wp_error($tmp)) {
                @unlink($tmp);
            } else {
                // save image
                $imageId = media_handle_sideload([
                    'name' => $artist->alias . ".$ext",
                    'tmp_name' => $tmp
                ], $id);

                set_post_thumbnail($id, $imageId);

                @unlink($tmp);
            }
        }
    }

    return ob_get_clean();
});

// locations
add_shortcode('lineupr-import-locations', function() {
    ob_start();

    // if not loggedin go out
    if(_wp_get_current_user()->user_login != 'jan') {
        print 'user jan has to be logged in<br />';
        return ob_get_clean();
    }

    // stop manually
    if(true) {
        print 'function manually stopped<br />';
        return ob_get_clean();
    }

    if (! function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (! function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }
    if (! function_exists('wp_read_image_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $lineupr = json_decode(file_get_contents(
        'https://neustadt-leben.lineupr.com/api/organizers/neustadt-leben/events/brn19/data'
    ));

    // import locations
    foreach($lineupr->venues as $location) {
        set_time_limit(0);

        // check lineupr-id of existing locations
        foreach(Eventkrake::getLocations(false) as $l) {
            $tags = Eventkrake::getSinglePostMeta($l->ID, 'tags');
            if(strpos($tags, $location->_id) > 0) continue 2;
        }

        // insert artist
        $description = '&nbsp;';
        if(! empty($location->descriptionHtml)) {
            $description = $location->descriptionHtml;
        }

        $id = wp_insert_post([
            'post_author'           => get_current_user_id(),
            'post_content'          => $description,
            'post_title'            => wp_strip_all_tags($location->name),
            'post_status'           => 'publish',
            'post_type'             => 'eventkrake_location',
            'post_name'             => $location->alias
        ]);
        if($id == 0) continue;

        print 'adding ' . wp_strip_all_tags($location->name) . '<br />';

        // lat lng
        Eventkrake::setSinglePostMeta($id, 'lat', $location->address->latitude);
        Eventkrake::setSinglePostMeta($id, 'lng', $location->address->longitude);

        // address
        $address = $location->address->street . ', '
            . $location->address->zip . ' '
            . $location->address->city;
        Eventkrake::setSinglePostMeta($id, 'address', $address);

        // tags
        Eventkrake::setSinglePostMeta($id, 'tags', "lineupr-id:{$location->_id}");

        // categories
        $categories = [];
        foreach($location->categories as $category) {
            foreach($lineupr->categories as $c) {
                if($c->_id == $category) {
                    $categories[] = $c->name;
                }
            }
        }
        Eventkrake::setPostMeta($id, 'categories', $categories);

        // links
        $links = [];
        foreach($location->attachments as $a) {
            $links[] = [
                'name' => $a->name,
                'url' => $a->link
            ];
        }
        Eventkrake::setSinglePostMeta($id, 'links', $links);

        // image
        if(isset($location->teaser) && isset($location->teaser->original)) {
            $url = 'https://lineupr.com' . $location->teaser->original;
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $tmp = download_url($url);

            if (is_wp_error($tmp)) {
                @unlink($tmp);
            } else {
                // save image
                $imageId = media_handle_sideload([
                    'name' => $location->alias . ".$ext",
                    'tmp_name' => $tmp
                ], $id);

                set_post_thumbnail($id, $imageId);

                @unlink($tmp);
            }
        }
    }

    return ob_get_clean();
});
