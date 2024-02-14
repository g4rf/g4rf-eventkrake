<?php

use Eventkrake\Config as Config;
use Eventkrake\Eventkrake as Eventkrake;
use Eventkrake\Event as Event;
use Eventkrake\Location as Location;

/*** add custom post type ***/

add_action('init', function () {
    register_post_type('eventkrake_event', array(
        'public' => true,
        'has_archive' => true,
        'taxonomies' => array('category'),
        'labels' => array(
            'name' => __('Veranstaltungen', 'eventkrake'),
            'singular_name' => __('Veranstaltung', 'eventkrake'),
            'add_new' => __('Veranstaltung anlegen', 'eventkrake'),
            'add_new_item' =>
                __('Neue Veranstaltung anlegen', 'eventkrake'),
            'edit' => __('Veranstaltung ändern', 'eventkrake'),
            'edit_item' => __('Veranstaltung ändern', 'eventkrake'),
            'new_item' => __('Veranstaltung anlegen', 'eventkrake'),
            'view' => __('Veranstaltung ansehen', 'eventkrake'),
            'search_items' => __('Veranstaltung suchen', 'eventkrake'),
            'not_found' =>
                __('Keine Veranstaltungen gefunden', 'eventkrake'),
            'not_found_in_trash' =>
                __('Keine gelöschten Veranstaltungen', 'eventkrake')
        ),
        'rewrite' => array('slug' => 'event'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugins_url( '/img/event.png', dirname(__FILE__) ),
        'description' => __('Veranstaltungen sind zeitlich begrenzte Ereignisse'
                . ' an einem Ort.', 'eventkrake'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail', 
            'comments'),
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // load meta box
            add_meta_box(
                'eventkrake_event',
                __('Weitere Angaben zur Veranstaltung', 'eventkrake'),
                function($args = null) {
                    // contents of meta box
                    include dirname(__FILE__) . '/../meta_event.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});


/*** save content of meta box ***/

add_action('save_post_eventkrake_event', function($post_id, $post) {

    // check user permissions
    if (!current_user_can('edit_post', $post->ID)) return;

    // check if executed on edit screen
    if(empty(filter_input(INPUT_POST, 'eventkrake_on_edit_screen'))) return;

    // don't save auto drafts
    if($post->post_status == 'auto-draft') return;

    // if this is just a revision, do nothing
    if (wp_is_post_revision($post_id)) return;

    // check POST fields
    $tags = filter_input(INPUT_POST, 'eventkrake_tags');
    
    // times
    $startDates = filter_input(INPUT_POST, 'eventkrake_startdate',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $startHours = filter_input(INPUT_POST, 'eventkrake_starthour',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $startMinutes = filter_input(INPUT_POST, 'eventkrake_startminute',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $endDates = filter_input(INPUT_POST, 'eventkrake_enddate',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $endHours = filter_input(INPUT_POST, 'eventkrake_endhour',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $endMinutes = filter_input(INPUT_POST, 'eventkrake_endminute',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    for($i = 1; $i < count($startDates); $i++) {
        $datesStart[] = 
            "{$startDates[$i]}T{$startHours[$i]}:{$startMinutes[$i]}:00";
        $datesEnd[] = 
            "{$endDates[$i]}T{$endHours[$i]}:{$endMinutes[$i]}:00";
    }

    // links
    $linksKeys = filter_input(INPUT_POST, 'eventkrake-links-key', 
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $linksValues = filter_input(INPUT_POST, 'eventkrake-links-value',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
    }

    // categories
    $postCategories = filter_input(INPUT_POST, 'eventkrake_categories', 
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $categories = [];
    if(! empty($postCategories))
    {
        $cats = explode(",", $postCategories);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // location id
    $postLocationId = filter_input(INPUT_POST, 'eventkrake_locationid');
    $locationId = empty($postLocationId) ? 0 : $postLocationId;

    // artists
    $postArtists = filter_input(INPUT_POST, 'eventkrake_artists',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $artists = empty($postArtists) ? [] : $postArtists;
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


/*** add accessibility information to the event post classes ***/

add_filter('post_class', function($classes, $class, $post_id) {
    if(is_admin()) return $classes;
    
    if (get_post_type() != 'eventkrake_event') {
        return $classes;
    }
    
    $location = Location::getLocationOfEvent($post_id);
    if($location != null) {
        $classes[] = "eventkrake-accessibility-{$location->accessibility}";
    }
    
    return $classes;
}, 10, 3);


/*** add event location and dates to event post excerpt ***/

add_filter( 'get_the_excerpt', function( $excerpt, $post ) {
    if(is_admin()) return $excerpt;
    if(get_post_type() != 'eventkrake_event') return $excerpt;
    
    if (!(is_single() || in_the_loop())) {
        return $excerpt; 
    }
    
    // times
    $times = Event::Factory($post->ID);
    if(count($times) == 0) return $excerpt;

    // no location
    if(empty($times[0]->location)) return $excerpt;
    
    // locale
    $locale = substr(get_locale(), 0, 2);
    // if WP MultiLang is installed
    if(function_exists('wpm_get_language')) {
        $locale = wpm_get_language();
    }

    // date+time formatter
    $dateFormatter = new IntlDateFormatter(
        $locale, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
    $timeFormatter = new IntlDateFormatter(
        $locale, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);

    // list start datetimes
    $list = [];
    $actualDate = 0; $string = '';
    foreach($times as $time) {

        // check if we are still on the same date
        if($actualDate == $time->start->format('Ymd')) {
            // add time to previous date
            $string .= ', ' . $timeFormatter->format($time->start);
            continue;
        }
        
        if(! empty($string)) $list[] = $string;
        $string = '';
        
        $actualDate = $time->start->format('Ymd');
        $string .= $dateFormatter->format($time->start) . ' ';
        $string .= $timeFormatter->format($time->start);
    }
    $list[] = $string;
    
    return implode(' // ', $list) .
        ' @ ' . get_the_title($times[0]->location) 
        . ": $excerpt";
    
// with 20 we do it after core stuff
}, 20, 2);


/*** add event location, dates and other meta to event post content ***/

add_filter('the_content', function($content) {
    if(is_admin()) return $content;
    if(get_post_type() != 'eventkrake_event') return $content;
    
    if (!(is_single() || in_the_loop())) {
        return $content; 
    }
    
    $times = Event::Factory(get_the_ID());
    if(count($times) == 0) return $content;

    ob_start(); ?>

    <div class="eventkrake-event-meta">

        <!-- location --><?php
        $location = new Location($times[0]->location); ?>
        <div class="eventkrake-event-location">
            <div class="eventkrake-event-location-title">
                <a href="<?=get_the_permalink($location->ID)?>"><?=
                    get_the_title($location->ID)
                ?></a>
            </div>
            <div class="eventkrake-event-location-address"><?=
                $location->address
            ?></div>
            <div class="eventkrake-accessibility-info"><?=
                $location->accessibilityInfo
            ?></div>
        </div>

        <!-- times --><?php
        $locale = substr(get_locale(), 0, 2);
        // if WP MultiLang is installed
        if(function_exists('wpm_get_language')) {
            $locale = wpm_get_language();
        }
        
        $dateFormatter = new IntlDateFormatter(
            $locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);        
        $timeFormatter = new IntlDateFormatter(
            $locale, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);

        ?><div class="eventkrake-event-times"><?php
            foreach($times as $time) { ?>
            
                <div class="eventkrake-event-time">
                    
                    <span class="eventkrake-event-start">
                        <span class="eventkrake-event-start-date"><?=
                            $dateFormatter->format($time->start)
                        ?></span>
                        <span class="eventkrake-event-start-time"><?=
                            $timeFormatter->format($time->start)
                        ?></span>
                    </span>
                    
                    <span class="eventkrake-event-end">
                        <span class="eventkrake-event-end-date"><?=
                            $dateFormatter->format($time->end)
                        ?></span>
                        <span class="eventkrake-event-end-time"><?=
                            $timeFormatter->format($time->end)
                        ?></span>
                    </span>
                    
                    <span class="eventkrake-event-ics">
                        <a href="/<?=$time->icsParameter()?>"><?=
                            __('ics', 'eventkrake')
                        ?></a>
                    </span>
                </div>
            
            <?php }
        ?></div>

    </div><?php
    
    // TODO: add other meta like artists, links, categories, tags

    return ob_get_clean() . $content;
});


/*** redirect to ics output ***/

add_action('template_redirect', function() {    
    $ics = filter_input(INPUT_GET, 'eventkrake_ics');
    $list = filter_input(INPUT_GET, 'eventkrake_ics_list');
    $id = filter_input(INPUT_GET, 'eventkrake_ics_id', FILTER_VALIDATE_INT);
    $index = filter_input(INPUT_GET, 'eventkrake_ics_index', 
        FILTER_VALIDATE_INT);
    
    $categories = filter_input(INPUT_GET, 'eventkrake_ics_categories',
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if(empty($categories)) $categories = [];
    
    $url = filter_input(INPUT_GET, 'eventkrake_ics_url');
    if(empty($url)) $url = '';
    
    // check if ics request
    if($ics != '1') return;
    
    // check if events list or single event
    if($list == '1') {
        $file = preg_replace('/[^a-z0-9]/i', '', get_bloginfo('name'));
        header('Content-Type: text/calendar; charset=utf-8');
        header("Content-Disposition: attachment; filename=$file-events.ics");

        print Event::icsAll($categories, $url);
        
        exit;
    }
    
    /*** single event ***/
    // check parameters
    if(! $id) return;
    if($index === NULL) return;
    
    // check if event
    try {
        $events = Event::Factory($id);
    } catch (Exception $ex) {
        return;
    }
    
    // select index
    $event = null;
    foreach($events as $e) {
        if($e->index == $index) {
            $event = $e;
            break;
        }
    }
    // index not found
    if ($event == null) return;
    
    // header
    $filename = preg_replace('/[^a-z0-9]/i', '', get_the_title($event->ID))
        . '-' . $event->ID . '-' . $event->index;
    header('Content-Type: text/calendar; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename.ics");

    print $event->ics($categories, $url);
    
    exit;
});