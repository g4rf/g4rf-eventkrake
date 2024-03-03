<?php
/**
 * Plugin Name:     Eventkrake
 * Plugin URI:      https://github.com/g4rf/g4rf-eventkrake
 * Description:     A wordpress plugin to manage events, locations and artists. It has an REST endpoint to use the data in external applications.
 * Author:          Jan Kossick
 * Version:         5.01beta
 * License:         CC BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
 * Author URI:      https://jankossick.de
 * Min WP Version:  6.1
 * Text Domain:     eventkrake
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
add_theme_support('post-thumbnails');

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
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        ['jquery']);
    wp_enqueue_script('eventkrake_leaflet');
    // general scripts
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        ['eventkrake_leaflet']);
    wp_enqueue_script('eventkrake');
    // admin scripts
    wp_register_script('eventkrake_admin', $path.'js/admin.js',
        ['jquery', 'eventkrake']);
    wp_enqueue_script('eventkrake_admin');

    // general css
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // admin css
    wp_register_style('eventkrake_admin', $path.'css/admin.css',
        ['eventkrake_all']);
    wp_enqueue_style('eventkrake_admin');
    // leaflet CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});

// frontend
add_action('wp_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // leaflet
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        ['jquery']);
    wp_enqueue_script('eventkrake_leaflet');
    // general scripts
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        ['eventkrake_leaflet']);
    wp_enqueue_script('eventkrake');

    // general css
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // leaflet css
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
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
    $id = $artist->ID;
    return [
        'id' => $id,
        'url' => get_permalink($id),
        'name' => get_the_title($id),
        'title' => get_the_title($id),
        'text' => apply_filters('the_content', $artist->post_content),
        'content' => apply_filters('the_content', $artist->post_content),
        'excerpt' => get_the_excerpt($id),
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
        'url' => get_permalink($id),
        'name' => get_the_title($id),
        'title' => get_the_title($id),
        'address' =>
            Eventkrake::getSinglePostMeta($id, 'address'),
        'lat' => Eventkrake::getSinglePostMeta($id, 'lat'),
        'lng' => Eventkrake::getSinglePostMeta($id, 'lng'),
        'text' => apply_filters('the_content', $location->post_content),
        'content' => apply_filters('the_content', $location->post_content),
        'excerpt' => get_the_excerpt($id),
        'image' =>  get_the_post_thumbnail_url($id, 'full'),
        'categories' => Eventkrake::getPostMeta($id, 'categories'),
        'links' => Eventkrake::getSinglePostMeta($id, 'links'),
        'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
    ];
}
function eventkrake_restbuild_event($post, $params = []) {
    // params
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

    // go through dates
    $dates = Event::Factory($post);
    $dateFormat = 'Y-m-d\TH:i:s';
    $events = [];
    foreach($dates as $date) {
        // check dates
        if($earliestStart != false && $date->start < $earliestStart) continue;
        if($earliestEnd != false && $date->end < $earliestEnd) continue;
        if($latestStart != false && $date->start > $latestStart) continue;
        if($latestEnd != false && $date->end > $latestEnd) continue;

        // add event
        $events[] = [
            'id' => $date->ID,
            'url' => get_permalink($date->ID),
            'name' => get_the_title($date->ID),
            'title' => get_the_title($date->ID),
            'text' => apply_filters('the_content', $date->content),
            'content' => apply_filters('the_content', $date->content),
            'excerpt' => get_the_excerpt($date->ID),
            'image' =>  get_the_post_thumbnail_url($date->ID, 'full'),
            'locationid' => $date->location,
            'locationId' => $date->location,
            'start' => $date->start->format($dateFormat),
            'end' => $date->end->format($dateFormat),
            'artists' => Eventkrake::getPostMeta($date->ID, 'artists'),
            'categories' => Eventkrake::getPostMeta($date->ID, 'categories'),
            'links' => Eventkrake::getSinglePostMeta($date->ID, 'links'),
            'tags' => Eventkrake::getSinglePostMeta($date->ID, 'tags'),
            'icsUrl' => get_site_url(
                            null, '/' . $date->icsParameter(), 'https')
        ];
    }

    return $events;
}

// sort events by date ASC
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
        'permission_callback' => '__return_true',
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
                            $a = get_post($artistId);
                            if($a) {
                                $artists[$artistId] = eventkrake_restbuild_artist($a);
                            }
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
        'permission_callback' => '__return_true',
        'args' => [
            'earliestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal date for the events. This parameter is checked against the start of an event.', 'eventkrake')
            ],
            'earliestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal date for the events. This parameter is checked against the end of an event.', 'eventkrake')
            ],
            'latestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal date for the events. This parameter is checked against the start of an event.', 'eventkrake')
            ],
            'latestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal date for the events. This parameter is checked against the end of an event.', 'eventkrake')
            ]
        ],
        'callback' => function($params) {
            $events = [];
            $locations = [];
            $artists = [];
            foreach(Eventkrake::getAllEvents() as $event) {
                $filteredEvents = eventkrake_restbuild_event($event, $params);
                if(! is_array($filteredEvents)) return $filteredEvents; // probably a WP_Error
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
                        $a = get_post($artistId);
                        if($a) {
                            $artists[$artistId] = eventkrake_restbuild_artist($a);
                        }
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
        'permission_callback' => '__return_true',
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
