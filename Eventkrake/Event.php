<?php

namespace Eventkrake;

class Event {
    var $ID;
    
    var $index; // counts the occurences of this event
    var $start;
    var $end;    
    var $door;
    
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
        $doors = self::getDoors($p->ID);
        
        for($i = 0; $i < count($starts); $i++) {
            $start = new \DateTimeImmutable($starts[$i]);
            $end = new \DateTimeImmutable($ends[$i]);
            
            $door = '';
            if(! empty($doors[$i])) $door = new \DateTimeImmutable($doors[$i]);
            
            $events[] = new self($post, $start, $end, $door, $i);
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
     * Get ends for a post id.
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
     * Get doors for a post id.
     * @param mixed $post ID or WP_Post
     * @return array
     */
    public static function getDoors($post) {
        if(is_scalar($post)) 
        {
            $id = $post;
        }
        elseif(! empty($post->ID))
        {
            $id = $post->ID;
        }
        
        $doors = Eventkrake::getPostMeta($id, 'door');
        
        // if doors not set yet, simulate empty doors
        if(count($doors) == 0) $doors = array_fill(0, 
            count(Eventkrake::getPostMeta($id, 'start')), '');
        
        return $doors;
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
    
    /**
     * 
     * @param mixed $post ID or WP_Post or Eventkrake\Event
     * @param array $doors Array of DateTime objects
     * @return mixed
     */
    public static function setDoors($post, $doors) {
        // do nothing
        if(empty($post)) return false;
        
        if(is_scalar($post)) $id = $post;
        elseif(! empty($post->ID)) $id = $post->ID;
        else return false;
        
        return Eventkrake::setPostMeta($id, 'door', $doors);
    }
    
    /**
     * 
     * @param type $categories
     * @param type $url
     * @return type
     */
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
     * @param DateTimeImmutable $start start date
     * @param DateTimeImmutable $end end date
     * @param DateTimeImmutable $door door time
     * @param type [$index=0] If the correponding post has more than one date, every date get's an index.
     * @throws \Exception If the post does not exists or is not of type `eventkrake_event`.
     */
    public function __construct($post, $start, $end, $door, $index = 0) {
        $p = get_post($post);
        
        if($p === null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_event') 
            throw new \Exception('Post type is not `eventkrake_event`.');
        
        $this->ID = $p->ID;
        $this->start = $start;
        $this->end = $end;
        $this->door = $door;
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
        $categories = get_the_category($this->ID);
        if(empty($categories)) return [];
        
        $return = [];
        foreach($categories as $category) {
            $return[] = $category->name;
        }
        return $return;
    }
    
    public function getWordpressTags() {
        $tags = get_the_terms($this->ID, 'post_tag');
        if(empty($tags)) return [];
        
        $return = [];
        foreach($tags as $tag) {
            $return[] = $tag->name;
        }
        return $return;
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
    
    public function getDoor() {
        return $this->door;
    }
    
    /**
     * Calculates, if the end is on the same day by respecting a "midnight shift".
     * @return bool
     */
    public function isEndOnSameDay() {
        $dayChange = Config::dayChange(); // time of "midnight"
        
        return $this->start->sub($dayChange)->format('Y-m-d')
                == $this->end->sub($dayChange)->format('Y-m-d');
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
        return Eventkrake::compatLinks(
            Eventkrake::getSinglePostMeta($this->ID, 'links'));
    }
    
    public function getCategories() {
        return Eventkrake::getPostMeta($this->ID, 'categories');
    }
    
    public function hideMeta() {
        return Config::hideEventMeta();
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
        'taxonomies' => ['category', 'post_tag'],
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
        'eventkrake_doorhour' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake_doorminute' => [
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
        
    ]);
    
    /* if properties is empty, don't do nothing
     * WHY? It's a strange behaviour with the block editor, that the save_post
     * hook is called not only once and sometimes with an empty $_POST.
     */
    if(empty($properties)) return;
    
    $event = new Event($post_id, new \DateTimeImmutable(), 
        new \DateTimeImmutable(), new \DateTimeImmutable);
    
    // dates & times
    $startDates = $properties['eventkrake_startdate'];
    $startHours = $properties['eventkrake_starthour'];
    $startMinutes = $properties['eventkrake_startminute'];
    $endDates = $properties['eventkrake_enddate'];
    $endHours = $properties['eventkrake_endhour'];
    $endMinutes = $properties['eventkrake_endminute'];
    $doorHours = $properties['eventkrake_doorhour'];
    $doorMinutes = $properties['eventkrake_doorminute'];
    $starts = [];
    $ends = [];
    $doors = [];
    // index 0 is the template
    for($i = 1; $i < count($startDates); $i++) 
    {
        // start
        $starts[] = 
            "{$startDates[$i]}T{$startHours[$i]}:{$startMinutes[$i]}:00";
        
        // end
        $ends[] = 
            "{$endDates[$i]}T{$endHours[$i]}:{$endMinutes[$i]}:00";
        
        // door
        if(empty($doorHours[$i])) {
            $doors[] =  '';
        } else {
            if(empty($doorMinutes[$i])) $doorMinutes[$i] = '00';
            $doors[] = 
                "{$startDates[$i]}T{$doorHours[$i]}:{$doorMinutes[$i]}:00";
        }
    }
    Event::setStarts($post_id, $starts);
    Event::setEnds($post_id, $ends);
    Event::setDoors($post_id, $doors);
    
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
    // array_filter filters empty values (like the template)
    $artists = array_filter($properties['eventkrake_artists']);
    $event->setArtists($artists);
    
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
    
    // bug: in block editor is_admin() is not working properly
    if(empty($event)) return $classes;
    
    if(($location = $event->getLocation()) !== false) {
        $classes[] = "eventkrake-accessibility-{$location->getAccessibility()}";
    }
    
    return $classes;
}, 10, 3);

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
    
    if($event->hideMeta()) return $content;
    
    $location = $event->getLocation();
    
    ob_start(); ?>
    
    <div class="eventkrake-event
                eventkrake-accessibility-<?=$location instanceof Location ? 
                                        $location->getAccessibility() : '' ?>">    

        <!-- location -->
        <div class="eventkrake-event-location"><?php
                        
            
            if($location instanceof Location) { ?>

                <!-- location title with link -->
                <div class="eventkrake-event-location-title-link
                            eventkrake-icon-before
                            eventkrake-wheelchair">
                    <a href="<?= $location->getPermalink() ?>"><?=
                        $location->getTitle();
                    ?></a>
                </div>

                <!-- location title (without link) -->
                <div class="eventkrake-event-location-title
                            eventkrake-icon-before
                            eventkrake-wheelchair"><?=
                        $location->getTitle();
                ?></div>

                <!-- location address with link -->
                <div class="eventkrake-event-location-address-link">
                    <a href="<?= $location->getAddressUrl() ?>"><?=
                        $location->getAddress();
                    ?></a>
                </div>

                <!-- location address (without link) -->
                <div class="eventkrake-event-location-address"><?=
                    $location->getAddress();
                ?></div>

                <!-- location accessibility info -->
                <div class="eventkrake-accessibility-info"><?=
                    $location->getAccessibilityInfo();
                ?></div>
                
            <?php } ?>
        </div>

        <!-- times -->
        <div class="eventkrake-event-times"><?php

            foreach($times as $time) { 
                
                // don't show if time has passed
                if($time->getEnd() < (new \DateTimeImmutable())) continue; ?>

                <div class="eventkrake-event-time
                            eventkrake-icon-before
                            eventkrake-icon-time">
                    
                    <!-- start -->
                    <span class="eventkrake-event-start">
                        <span class="eventkrake-event-start-date"><?=
                            Eventkrake::formatDate($time->getStart())
                        ?></span>
                        <span class="eventkrake-event-start-time"><?=
                            Eventkrake::formatTime($time->getStart())
                        ?></span>
                    </span>

                    <!-- end -->
                    <span class="eventkrake-event-end">
                        <?php if(! $time->isEndOnSameDay()) { ?>
                            <span class="eventkrake-event-end-date"><?=
                                Eventkrake::formatDate($time->getEnd())
                            ?></span>
                        <?php } ?>
                        <span class="eventkrake-event-end-time"><?=
                            Eventkrake::formatTime($time->getEnd())
                        ?></span>
                    </span>
                    
                    <!-- door -->
                    <?php if(! empty($time->getDoor())) { ?>
                    
                        <span class="eventkrake-door">
                            <?=__('Doors:', 'eventkrake')?>
                            <span class="eventkrake-door-time"><?=
                                Eventkrake::formatTime($time->getDoor())
                            ?></span>
                        </span>
                    
                    <?php } ?>

                    <!-- ics -->
                    <span class="eventkrake-event-ics eventkrake-ics">
                        <a href="/<?=$time->icsParameter()?>"><?=
                            __('ics', 'eventkrake')
                        ?></a>
                    </span>

                </div>

            <?php }
        ?></div>

        <!-- wp tags -->
        <div class="eventkrake-event-wp-tags eventkrake-tags"><?=
            implode(', ', $event->getWordpressTags());
        ?></div>
        
        <!-- wp categories -->
        <div class="eventkrake-event-wp-categories eventkrake-tags"><?=
            implode(', ', $event->getWordpressCategories());
        ?></div>
        
        <!-- categories -->
        <div class="eventkrake-event-categories eventkrake-tags"><?=
            implode(', ', $event->getCategories());
        ?></div>
        
        <!-- links -->
        <div class="eventkrake-event-links"><?php
            
            $eventLinks = $event->getLinks();
            
            if(! empty($eventLinks)) { ?>
        
                <h3 class="eventkrake-event-links-headline"><?= 
                    __('Further information', 'eventkrake')
                ?></h3>
            
                <ul>
                    <?php foreach($eventLinks as $link) { ?>

                        <li><a class="eventkrake-event-link"
                           href="<?= $link->url ?>"><?=
                                $link->name
                        ?></a></li>

                    <?php } ?>
                </ul>
            
            <?php } ?>
            
        </div>
        
        <!-- artists -->
        <div class="eventkrake-event-artists"><?php 
        
            $eventArtists = $event->getArtists();

            if(! empty($eventArtists)) { ?>
        
                <h3 class="eventkrake-event-artists-headline"><?= 
                    sprintf(
                        /* translators: Placeholder is event title */
                        __('Participating artists at %s', 'eventkrake'), 
                        $event->getTitle()
                    ) 
                ?></h3>

                <?php foreach($eventArtists as $artist) { ?>

                    <div class="eventkrake-event-artist">

                        <!-- artist name & link -->
                        <div class="eventkrake-event-artist-title-link">
                            <a href="<?= $artist->getPermalink() ?>"><?=
                                $artist->getTitle();
                        ?></a></div>

                        <!-- artist name (without link) -->
                        <div class="eventkrake-event-artist-title"><?=
                            $artist->getTitle();
                        ?></div>


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
            } ?>
        </div>
        

        <!-- event image -->
        <div class="eventkrake-event-image"><?php
            if (has_post_thumbnail($event->ID)) {
                print get_the_post_thumbnail($event->ID, 'large');
            }
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


/**
 * @deprecated
 * add event location and dates to event post excerpt 
 */
add_filter( 'get_the_excerpt', function( $excerpt, $post ) {
    /**
     * We don't use it, as excerpts should be clean. In addition it leads to
     * double informations when events are listed at locations and artists.
     * 
     * Just leave it here for reference, how it *may* be done.
     */
    return $excerpt;
    
    if(is_admin()) return $excerpt;

    if(get_post_type($post->ID) != 'eventkrake_event') return $excerpt;
    
    if (!(is_single() || in_the_loop())) {
        return $excerpt; 
    }

    // TODO: Function does not exist.
    if(Config::hideEventExcerptMeta()) return $excerpt;
    
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
    $dateFormatter = new \IntlDateFormatter(
        $locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
    $timeFormatter = new \IntlDateFormatter(
        $locale, \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);

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