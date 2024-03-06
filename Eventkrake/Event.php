<?php

namespace Eventkrake;

class Event {
    var $ID;
    
    var $index; // counts the occurences of this event
    var $start;
    var $end;    
    
    /*
     * static
     */
    
    /**
     * Returns an array of events from one post, as a post can have multiple
     * occurences.
     * @param WP_Post $post
     * @return array Array of Eventkrake\Events
     * @throws \Exception If post not exist or is not of type eventkrake_event
     */
    public static function Factory($post) {
        $p = get_post($post);
        
        if($p == null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_event') 
            throw new \Exception('Post type is not `eventkrake_event`.');
        
        $events = [];
        $starts = self::getStarts($p->ID);
        $ends = self::getEnds($p->ID);
        
        for($i = 0; $i < count($starts); $i++) {
            $start = new \DateTime($starts[$i]);
            $end = new \DateTime($ends[$i]);
            
            $events[] = new self($post, $start, $end, $i);
        }
        
        return $events;
    }
    
    /**
     * Sort events ascending by start datetime.
     * @param array $events Array of Eventkrake\Event
     * @return array The sorted $events
     */
    public static function sort($events) {
        usort($events, function($a, $b) {
            
            if($a->getStart() < $b->getStart()) return -1;
            if($a->getStart() > $b->getStart()) return 1;
            return 0;
            
        });
        
        return $events;
    }
    
    /**
     * Returns list of all events.
     * @param array [$filter=[]] @see https://developer.wordpress.org/reference/functions/get_posts/
     * @return array Array of all events, sorted.
     */
    public static function all($filter = []) {
        $options = [
            'numberposts' => -1,
            'offset' => 0,
            'post_type' => 'eventkrake_event'
        ];
        $posts = get_posts(array_merge($options, $filter));

        $events = [];
        foreach($posts as $post) {
            $events = array_merge($events, Event::Factory($post));
        }
        return self::sort($events);
    }
    
    /**
     * Get starts for a post id.
     * @param mixed $post ID or WP_Post
     * @return array
     */
    public static function getStarts($post) {
        if(is_scalar($post)) 
        {
            $id = $post;
        }
        elseif(! empty($post->ID))
        {
            $id = $post->ID;
        }
        
        return Eventkrake::getPostMeta($id, 'start');
    }
    
    /**
     * Get starts for a post id.
     * @param mixed $post ID or WP_Post
     * @return array
     */
    public static function getEnds($post) {
        if(is_scalar($post)) 
        {
            $id = $post;
        }
        elseif(! empty($post->ID))
        {
            $id = $post->ID;
        }
        
        return Eventkrake::getPostMeta($id, 'end');
    }
    
    /**
     * 
     * @param mixed $post ID or WP_Post or Eventkrake\Event
     * @param array $starts Array of DateTime objects
     * @return nixed
     */
    public static function setStarts($post, $starts) {
        // do nothing
        if(empty($post)) return false;
        
        if(is_scalar($post)) $id = $post;
        elseif(! empty($post->ID)) $id = $post->ID;
        else return false;
        
        return Eventkrake::setPostMeta($id, 'start', $starts);
    }
    
    /**
     * 
     * @param mixed $post ID or WP_Post or Eventkrake\Event
     * @param array $ends Array of DateTime objects
     * @return nixed
     */
    public static function setEnds($post, $ends) {
        // do nothing
        if(empty($post)) return false;
        
        if(is_scalar($post)) $id = $post;
        elseif(! empty($post->ID)) $id = $post->ID;
        else return false;
        
        return Eventkrake::setPostMeta($id, 'end', $ends);
    }
    
    public static function icsAll($categories = [], $url = '') {
        $ics = ['BEGIN:VCALENDAR', 
            'VERSION:2.0',
            self::icsEscapeKeyValue(
                'PRODID',
                'Eventkrake Wordpress Plugin @ ' . get_bloginfo('name')
            ),
            'METHOD:PUBLISH'];

        $now = new \DateTime();
        foreach(Eventkrake::events() as $event) {
            if($event->getEnd() < $now) continue;
            
            $ics[] = $event->icsEvent($categories, $url);
        }
            
        $ics[] = 'END:VCALENDAR';
        
        return implode("\r\n", $ics);
    }
    
    /*
     * construct
     */
    
    /**
     * Creates an event.
     * @param type $post The corresponding post or post id.
     * @param type $start DateTime start date
     * @param type $end DateTime end date
     * @param type [$index=0] If the correponding post has more than one date, every date get's an index.
     * @throws \Exception If the post does not exists or is not of type `eventkrake_event`.
     */
    public function __construct($post, $start, $end, $index = 0) {
        $p = get_post($post);
        
        if($p === null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_event') 
            throw new \Exception('Post type is not `eventkrake_event`.');
        
        $this->ID = $p->ID;
        $this->start = $start;
        $this->end = $end;
        $this->index = $index;
    }
    
    /*
     * getters
     */
    
    public function getUID() {
        return $this->ID . '-' . $this->getIndex();
    }
    
    public function getTitle() {
        return get_the_title($this->ID); 
    }
    
    public function getContent() {
        return apply_filters('the_content', 
            get_the_content(null, false, $this->ID));
    }
    
    public function getExcerpt() {
        if(has_excerpt($this->ID)) return get_the_excerpt($this->ID);
        return '';
    }
    
    public function getSlug() {
        return get_post_field('post_name', $this->ID);
    }
    
    public function getPermalink() {
        return get_the_permalink($this->ID);
    }
    
    public function getWordpressCategories() {
        return wp_get_post_categories($this->ID, ['fields' => 'all']);
    }
    
    public function getWordpressTags() {
        return wp_get_post_tags($this->ID);
    }
    
    public function getLocationId() {
        return Eventkrake::getSinglePostMeta($this->ID, 'locationid');
    }
    
    public function getIndex() {
        return $this->index;
    }
    
    public function getStart() {
        return $this->start;
    }
    
    public function getEnd() {
        return $this->end;
    }    
    
    public function getLocation() {
        try {
            return new Location($this->getLocationId());
        } catch(\Exception $ex) {
            return false;
        }
    }
    
    public function getArtistIds() {
        return Eventkrake::getPostMeta($this->ID, 'artists');
    }
    
    public function getArtists() {
        $artistIds = $this->getArtistIds();
        $artists = [];
        foreach($artistIds as $id) {
            $artists[] = new Artist($id);
        }
        return $artists;
    }
    
    public function getLinks() {
        $links = Eventkrake::getSinglePostMeta($this->ID, 'links');
        $return = [];
        foreach($links as $link) 
        {
            if(is_array($link)) 
            {
                $return[] = new Link($link['name'], $link['url']);
            }
            elseif(is_object($link)) 
            {
                $return[] = $link;
            }
        }
        return $return; 
    }
    
    public function getCategories() {
        return Eventkrake::getPostMeta($this->ID, 'categories');
    }
        
    public function getTags() {
        return Eventkrake::getSinglePostMeta($this->ID, 'tags');
    }
    
    /*
     * setters
     */
    
    /**
     * 
     * @param mixed $location ID (scalar), Eventkrake\Location or WP_Post
     * @return mixed
     */
    public function setLocation($location) {
        // nothing to save
        if(empty($location)) return false;
        
        if(is_scalar($location) && $location != 0)
        {
            $id = $location;
        }
        elseif(! empty($location->ID)) {
            // object with ID given, e.g. Location or WP_Post
            $id = $location->ID;
        } 
        else return false; // object with no ID        
        
        return Eventkrake::setSinglePostMeta($this->ID, 'locationid', $id);
    }
    
    /**
     * 
     * @param array $artists Array of ID (scalar), Eventkrake\Artist or WP_Post
     * @return mixed
     */
    public function setArtists($artists) {
        // nothing to save
        if(empty($artists)) return false;
        
        $ids = [];
        foreach($artists as $artist)
        {
            if(is_scalar($artist) && $artist != 0)
            {
                $ids[] = $artist;
            } 
            elseif(! empty($artist->ID)) 
            {
                $ids[] = $artist->ID;
            }
        }
        
        return Eventkrake::setPostMeta($this->ID, 'artists', $ids);
    }
        
    /**
     * 
     * @param array $links
     */
    public function setLinks($links) {
        return Eventkrake::setSinglePostMeta($this->ID, 'links', $links);
    }
    
    /**
     * 
     * @param array $categories
     */
    public function setCategories($categories) {
        return Eventkrake::setPostMeta($this->ID, 'categories', $categories);
    }
        
    /**
     * 
     * @param string $tags
     */
    public function setTags($tags) {
        return Eventkrake::setSinglePostMeta($this->ID, 'tags', $tags);
    }
    
    /*
     * ics
     */

    /**
     * 
     * @param type $categories
     * @param type $url
     * @return type
     */
    public function ics($categories = [], $url = '') {
        $ics = [
            'BEGIN:VCALENDAR',
                'VERSION:2.0',
                self::icsEscapeKeyValue(
                    'PRODID',
                    'Eventkrake Wordpress Plugin @ ' . get_bloginfo('name')
                ),
                'METHOD:PUBLISH',

                $this->icsEvent($categories, $url),
            
            'END:VCALENDAR'
        ];
        
        return implode("\r\n", $ics);
    }
    
    /**
     * 
     * @param type $categories
     * @param type $url
     * @return type
     */
    public function icsEvent($categories = [], $url = '') {
        $dateFormat = 'Ymd\THis'; // no time zone set
        
        if(empty($url)) {
            $url = $this->getPermalink();
        }
        
        $cats = [];
        foreach($categories as $c) {
            $cats[] = $this->icsEscapeString($c);
        }
        
        $location = $this->getLocation();
        
        // excerpt
        $excerpt = '';
        if(has_excerpt($this->ID)) {
            $excerpt = html_entity_decode(
                wp_strip_all_tags(get_the_excerpt($this->ID), true),
                ENT_HTML5,
                'UTF-8'
            );
        }
        
        $ics = [ 
            'BEGIN:VEVENT',
                'CLASS:PUBLIC',
                'TRANSP:OPAQUE', // busy
                self::icsEscapeKeyValue(
                    'UID',
                    'ID' . $this->ID . '-' . $this->getIndex() .
                    '@' . parse_url(get_site_url(), PHP_URL_HOST)
                ),
                'CATEGORIES:' . implode(',', $cats),
                self::icsEscapeKeyValue(
                    'LOCATION',
                    html_entity_decode(
                        wp_strip_all_tags(
                            get_the_title($location->ID) . 
                            ' (' . $location->getAddress() . ')'
                            , true),
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                'GEO:' . $location->getLat() . ';' . $location->getLng(),
                self::icsEscapeKeyValue(
                    'SUMMARY',
                    html_entity_decode(
                        wp_strip_all_tags(get_the_title($this->ID), true),
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                self::icsEscapeKeyValue('DESCRIPTION', $excerpt),
                self::icsEscapeKeyValue('URL', $url),
                'DTSTART:' . $this->getStart()->format($dateFormat),
                'DTEND:' . $this->getEnd()->format($dateFormat),
                'DTSTAMP:' . (new \DateTime())->format($dateFormat),
            'END:VEVENT'
        ];
        
        return implode("\r\n", $ics);
    }
    
    /**
     * 
     * @param type $categories
     * @param type $url
     * @return type
     */
    public function icsParameter($categories = [], $url = '') {
        return '?' . http_build_query([
            'eventkrake_ics' => '1',
            'eventkrake_ics_id' => $this->ID,
            'eventkrake_ics_index' => $this->getIndex(),
            'eventkrake_ics_categories' => $categories,
            'eventkrake_ics_url' => $url
        ]);
    }
    
    /*
     * private functions
     */
    
    /**
     * 
     * @param type $key
     * @param type $value
     * @return type
     */
    private static function icsEscapeKeyValue($key, $value) {
        $sanitized = "$key:" . self::icsEscapeString($value);
        $chunks = mb_str_split($sanitized, 73, 'UTF-8');
        return  implode("\r\n ", $chunks);
    }
    
    /**
     * 
     * @param type $value
     * @return type
     */
    private static function icsEscapeString($value) {
        return preg_replace('/([\,;])/', '\\\$1', $value);
    }
}

/*
 * add custom post type 
 */
add_action('init', function () {
    register_post_type('eventkrake_event', [
        'public' => true,
        'has_archive' => true,
        'taxonomies' => ['category', 'tag'],
        'labels' => [
            'name' => __('Events', 'eventkrake'),
            'singular_name' => __('Event', 'eventkrake'),
            'add_new' => __('Add event', 'eventkrake'),
            'add_new_item' =>
                __('Add new event', 'eventkrake'),
            'edit' => __('Edit event', 'eventkrake'),
            'edit_item' => __('Edit event', 'eventkrake'),
            'new_item' => __('Add event', 'eventkrake'),
            'view' => __('View event', 'eventkrake'),
            'search_items' => __('Search for event', 'eventkrake'),
            'not_found' =>
                __('No events found', 'eventkrake'),
            'not_found_in_trash' =>
                __('No even in trash', 'eventkrake')
        ],
        'rewrite' => ['slug' => 'event'],
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugins_url( '/img/event.png', dirname(__FILE__) ),
        'description' => __('Events are temporary occurrences in one place.',
            'eventkrake'),
        'supports' => ['title', 'excerpt', 'editor', 'thumbnail', 
            'comments'],
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // load meta box
            add_meta_box(
                'eventkrake_event',
                __('Additional informations', 'eventkrake'),
                function($args = null) {
                    // contents of meta box
                    include dirname(__FILE__) . '/../metabox/event.php';
                }, null, 'normal', 'high', null
            );
        }
    ]);
});


/*
 * save content of meta box
 */
add_action('save_post_eventkrake_event', function($post_id, $post) {

    // check user permissions
    if (!current_user_can('edit_post', $post_id)) return;

    // don't save auto drafts
    if($post->post_status == 'auto-draft') return;

    // if this is just a revision, do nothing
    if (wp_is_post_revision($post_id)) return;

    // get $_POST
    $properties = filter_input_array(INPUT_POST, [

        // location
        'eventkrake_locationid' => FILTER_DEFAULT,
        
        // dates & times
        'eventkrake_startdate' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake_starthour' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake_startminute' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake_enddate' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake_endhour' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake_endminute' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        
        // artists
        'eventkrake_artists' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        
        // links
        'eventkrake-links-key' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake-links-value' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        
        // categories
        'eventkrake_categories' => FILTER_DEFAULT,
        
        // tags
        'eventkrake_tags' => FILTER_DEFAULT,
        
    ]);
    
    /* if properties is empty, don't do nothing
     * WHY? It's a strange behaviour with the block editor, that the save_post
     * hook is called not only once and sometimes with an empty $_POST.
     */
    if(empty($properties)) return;
    
    $event = new Event($post_id, new \DateTime(), new \DateTime());
    
    // tags
    $event->setTags($properties['eventkrake_tags']);
    
    // dates & times
    $startDates = $properties['eventkrake_startdate'];
    $startHours = $properties['eventkrake_starthour'];
    $startMinutes = $properties['eventkrake_startminute'];
    $endDates = $properties['eventkrake_enddate'];
    $endHours = $properties['eventkrake_endhour'];
    $endMinutes = $properties['eventkrake_endminute'];
    $starts = [];
    $ends = [];
    // index 0 is the template
    for($i = 1; $i < count($startDates); $i++) {
        $starts[] = 
            "{$startDates[$i]}T{$startHours[$i]}:{$startMinutes[$i]}:00";
        $ends[] = 
            "{$endDates[$i]}T{$endHours[$i]}:{$endMinutes[$i]}:00";
    }
    Event::setStarts($post_id, $starts);
    Event::setEnds($post_id, $ends);
    
    // links
    $linkNames = $properties['eventkrake-links-key'];
    $linkUrls = $properties['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linkNames); $i++) {
        if(empty($linkNames[$i])) continue;
        if(empty($linkUrls[$i])) continue;

        $links[] = [
            'name' => $linkNames[$i], 
            'url' => $linkUrls[$i]
        ];
    }
    $event->setLinks($links);

    // categories
    $categories = [];
    if(! empty($properties['eventkrake_categories']))
    {
        $cats = explode(",", $properties['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }
    $event->setCategories($categories);

    // location
    $event->setLocation($properties['eventkrake_locationid']);

    // artists
    $event->setArtists($properties['eventkrake_artists']);
    
}, 1, 2);


/*
 * add accessibility information to the event post classes 
 */
add_filter('post_class', function($classes, $class, $post_id) {
    if(is_admin()) return $classes;
    
    if (get_post_type() != 'eventkrake_event') {
        return $classes;
    }
    
    $event = Event::Factory($post_id)[0];
    if(($location = $event->getLocation()) !== false) {
        $classes[] = "eventkrake-accessibility-{$location->getAccessibility()}";
    }
    
    return $classes;
}, 10, 3);


/*
 * add event location and dates to event post excerpt 
 */
add_filter( 'get_the_excerpt', function( $excerpt, $post ) {
    if(is_admin()) return $excerpt;

    if(get_post_type($post->ID) != 'eventkrake_event') return $excerpt;
    
    if (!(is_single() || in_the_loop())) {
        return $excerpt; 
    }
    
    // times
    $times = Event::Factory($post->ID);
    if(count($times) == 0) return $excerpt;

    // no location
    if(empty($times[0]->getLocationId())) return $excerpt;
    
    // locale
    $locale = substr(get_locale(), 0, 2);
    // if WP Multilang is installed
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
        if($actualDate == $time->getStart()->format('Ymd')) {
            // add time to previous date
            $string .= ', ' . $timeFormatter->format($time->getStart());
            continue;
        }
        
        if(! empty($string)) $list[] = $string;
        $string = '';
        
        $actualDate = $time->getStart()->format('Ymd');
        $string .= $dateFormatter->format($time->getStart()) . ' ';
        $string .= $timeFormatter->format($time->getStart());
    }
    $list[] = $string;
    
    return implode(' // ', $list) .
        ' @ ' . get_the_title($times[0]->getLocationId()) 
        . ": $excerpt";
    
// with 20 we do it after core stuff
}, 20, 2);


/*
 * add event location, dates and other meta to event post content 
 */
add_filter('the_content', function($content)
{
    if(is_admin()) return $content;
    if(get_post_type() != 'eventkrake_event') return $content;
    
    if (!(is_single() || in_the_loop())) {
        return $content; 
    }
    
    try {
        $times = Event::Factory(get_the_ID());
    } catch(\Exception $ex) {
        return $content;
    }
    
    if(count($times) == 0) return $content;
    
    $event = $times[0];
    
    ob_start(); ?>

    <div class="eventkrake-event">    

        <!-- location --><?php
        $location = $event->getLocation(); ?>
        <div class="eventkrake-event-location">
            <!-- location title -->
            <div class="eventkrake-event-location-title">
                <a href="<?= $location->getPermalink() ?>"><?=
                    $location->getTitle();
                ?></a>
            </div>
            <!-- location address (without link) -->
            <div class="eventkrake-event-location-address"><?=
                $location->getAddress();
            ?></div>
            <!-- location addres with link -->
            <div class="eventkrake-event-location-address-with-link">
                <a href="<?= $location->getAddressUrl() ?>"><?=
                    $location->getAddress();
                ?></a>
            </div>
            <!-- location accessibility info -->
            <div class="eventkrake-accessibility-info"><?=
                $location->getAccessibilityInfo();
            ?></div>
        </div>

        <!-- times --><?php
        $locale = substr(get_locale(), 0, 2);
        // if WP MultiLang is installed
        if(function_exists('wpm_get_language')) {
            $locale = wpm_get_language();
        }
        
        $dateFormatter = new \IntlDateFormatter(
            $locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);        
        $timeFormatter = new \IntlDateFormatter(
            $locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);

        ?><div class="eventkrake-event-times"><?php
            foreach($times as $time) { ?>
            
                <div class="eventkrake-event-time">
                    
                    <span class="eventkrake-event-start">
                        <span class="eventkrake-event-start-date"><?=
                            $dateFormatter->format($time->getStart())
                        ?></span>
                        <span class="eventkrake-event-start-time"><?=
                            $timeFormatter->format($time->getStart())
                        ?></span>
                    </span>
                    
                    <span class="eventkrake-event-end">
                        <span class="eventkrake-event-end-date"><?=
                            $dateFormatter->format($time->getEnd())
                        ?></span>
                        <span class="eventkrake-event-end-time"><?=
                            $timeFormatter->format($time->getEnd())
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

        <!-- tags -->
        <div class="eventkrake-event-tags"><?=
            $event->getTags();
        ?></div>
        
        <!-- categories -->
        <div class="eventkrake-event-categories"><?=
            implode(', ', $event->getCategories());
        ?></div>

        <!-- links -->
        <ul class="eventkrake-event-links">
            <?php foreach($event->getLinks() as $link) { ?>
            
                <li><a class="eventkrake-event-link"
                   href="<?= $link->url ?>"><?=
                        $link->name
                ?></a></li>
                            
            <?php } ?>
        </ul>
        
        <!-- artists -->
        <div class="eventkrake-event-artists"><?php
            
            foreach($event->getArtists() as $artist) { ?>
            
                <div class="eventkrake-event-artist">
                    
                    <!-- artist name & link -->
                    <div class="eventkrake-event-artist-title">
                        <a href="<?= $artist->getPermalink() ?>"><?=
                            $artist->getTitle();
                    ?></a></div>
                    
                    <!-- artist excerpt -->
                    <div class="eventkrake-event-artist-excerpt"><?=
                        wpautop($artist->getExcerpt())
                    ?></div>
                    
                    <!-- artist image -->
                    <div class="eventkrake-event-artist-image"><?php
                        if (has_post_thumbnail($artist->ID)) {
                            print get_the_post_thumbnail($artist->ID, 'large');
                        }
                    ?></div>
                </div>
            
            <?php }
        ?></div>

        <!-- content -->
        <div class="eventkrake-event-content"><?=
            $content
        ?></div>
        
    </div><?php
    
    return  ob_get_clean();
}, 10, 2);


/*
 * redirect to ics output 
 */
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
        if($e->getIndex() == $index) {
            $event = $e;
            break;
        }
    }
    // index not found
    if ($event == null) return;
    
    // header
    $filename = preg_replace('/[^a-z0-9]/i', '', $event->getTitle())
        . '-' . $event->ID . '-' . $event->getIndex();
    header('Content-Type: text/calendar; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename.ics");

    print $event->ics($categories, $url);
    
    exit;
});