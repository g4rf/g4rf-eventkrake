<?php

namespace Eventkrake;

class Location {
    var $ID;
    
    /*
     * static
     */
    
    /**
     * Returns list of all locations.
     * @param array [$filter=[]] @see https://developer.wordpress.org/reference/functions/get_posts/
     * @return array Array of all locations, sorted by title.
     */
    public static function all($filter = []) {
        $options = [
            'numberposts' => -1,
            'offset' => 0,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => 'eventkrake_location'
        ];
        $posts = get_posts(array_merge($options, $filter));
        
        $locations = [];
        foreach($posts as $post) {
            $locations[] = new Location($post);
        }
        
        return $locations;
    }
    
    /*
     * construct
     */
    
    public function __construct($post) {
        $p = get_post($post);
        
        if($p == null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_location') 
            throw new \Exception('Post type is not `eventkrake_location`.');
        
        $this->ID = $p->ID;
    }
    
    /*
     * getters
     */
    
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
    
    public function getLat() {
        return Eventkrake::getSinglePostMeta($this->ID, 'lat');
    }
    
    public function getLng() {
        return Eventkrake::getSinglePostMeta($this->ID, 'lng');
    }
    
    public function getAddress() {
        return Eventkrake::getSinglePostMeta($this->ID, 'address');
    }
    
    /**
     * Returns an OpenStreetMap link to the address.
     * @param integer $zoom The map zoom
     * @return string The url.
     */
    public function getAddressUrl() {
        return sprintf(Config::locationAddressUrlTemplate(),
            $this->getLat(), 
            $this->getLng(),
            Config::locationAddressUrlTemplateZoom());
    }
    
    public function getLinks() {
        return Eventkrake::getSinglePostMeta($this->ID, 'links');
    }
    
    public function getCategories() {
        return Eventkrake::getPostMeta($this->ID, 'categories');
    }
    
    public function getAccessibility() {
        return Eventkrake::getSinglePostMeta($this->ID, 'accessibility');
    }
    
    public function getAccessibilityInfo() {
        return Eventkrake::getSinglePostMeta($this->ID, 'accessibility-info');
    }
    
    public function getTags() {
        return Eventkrake::getSinglePostMeta($this->ID, 'tags');
    }    
    
    public function getEvents() {
        $posts = get_posts([
            'numberposts' => -1,
            'offset' => 0,
            'post_type' => 'eventkrake_event',
            'meta_query' => [
                [
                    'key' => 'eventkrake_locationid',
                    'value' => $this->ID
                ]
            ]
        ]);
        $events = [];
        foreach($posts as $post) {
            $events = array_merge($events, Event::Factory($post));
        }
        
        return Event::sort($events);
    }
    
    /*
     * setters
     */
    
    public function setLat($lat) {
        return Eventkrake::setSinglePostMeta($this->ID, 'lat', $lat);
    }
    
    public function setLng($lng) {
        return Eventkrake::setSinglePostMeta($this->ID, 'lng', $lng);
    }
    
    public function setAddress($address) {
        return Eventkrake::setSinglePostMeta($this->ID, 'address', $address);
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
    
    public function setAccessibility($accessibility) {
        return Eventkrake::setSinglePostMeta(
            $this->ID, 'accessibility', $accessibility);
    }
    
    public function setAccessibilityInfo($accessibilityInfo) {
        return Eventkrake::setSinglePostMeta(
            $this->ID, 'accessibility-info', $accessibilityInfo);
    }    
}

/*
 * add custom post type 
 */
add_action('init', function () {
    register_post_type('eventkrake_location', [
        'public' => true,
        'has_archive' => true,
        'taxonomies' => ['category', 'tag'],
        'labels' => [
            'name' => __('Orte', 'eventkrake'),
            'singular_name' => __('Ort', 'eventkrake'),
            'add_new' => __('Ort hinzufügen', 'eventkrake'),
            'add_new_item' => __('Neuen Ort hinzufügen', 'eventkrake'),
            'edit' => __('Ort bearbeiten', 'eventkrake'),
            'edit_item' => __('Ort bearbeiten', 'eventkrake'),
            'new_item' => __('Ort hinzufügen', 'eventkrake'),
            'view' => __('Ort anschauen', 'eventkrake'),
            'search_items' => __('Ort suchen', 'eventkrake'),
            'not_found' => __('Keine Orte gefunden', 'eventkrake'),
            'not_found_in_trash' =>
                __('Keine gelöschten Orte', 'eventkrake')
        ],
        'rewrite' => ['slug' => 'location'],
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugins_url( '/img/location.png', dirname(__FILE__) ),
        'description' =>
            __('An Orten finden Veranstaltungen statt.', 'eventkrake'),
        'supports' => ['title', 'excerpt', 'editor', 'thumbnail', 
            'comments'],
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // load meta box
            add_meta_box(
                'eventkrake_location',
                __('Weitere Angaben zum Ort', 'eventkrake'),
                function($args = null) {
                    include dirname(__FILE__) . '/../metabox/location.php';
                }, null, 'normal', 'high', null
            );
        }
    ]);
});

/*
 * save content of meta box
 */
add_action('save_post_eventkrake_location', function($post_id, $post) {

    // check user permissions
    if (!current_user_can('edit_post', $post_id)) return;

    // don't save auto drafts
    if($post->post_status == 'auto-draft') return;

    // if this is just a revision, do nothing
    if (wp_is_post_revision($post_id)) return;
    
    // get $_POST
    $properties = filter_input_array(INPUT_POST, [
        
        // links
        'eventkrake-links-key' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        'eventkrake-links-value' => [
            'filter' => FILTER_DEFAULT,
            'flags'  => FILTER_REQUIRE_ARRAY,
        ],
        
        // lat
        'eventkrake_lat' => FILTER_DEFAULT,
        
        // lng
        'eventkrake_lng' => FILTER_DEFAULT,

        // address
        'eventkrake_address' => FILTER_DEFAULT,
        
        // accessibility
        'eventkrake-accessibility' => FILTER_DEFAULT,
        
        // accessibility info
        'eventkrake-accessibility-info' => FILTER_DEFAULT,
        
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
    
    $location = new Location($post_id);
    
    // set simple fields
    $location->setLat($properties['eventkrake_lat']);
    $location->setLng($properties['eventkrake_lng']);
    $location->setAddress($properties['eventkrake_address']);
    $location->setAccessibility($properties['eventkrake-accessibility']);
    $location->setAccessibilityInfo($properties['eventkrake-accessibility-info']);
    $location->setTags($properties['eventkrake_tags']);
    
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
    $location->setLinks($links);

    // categories
    $categories = [];
    if(! empty($properties['eventkrake_categories'])) {
        $cats = explode(",", $properties['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }
    $location->setCategories($categories);
    
}, 1, 2);

/*
 *  add location address and date to event page
 */
add_filter('the_content', function($content)
{
    if(is_admin()) return $content;
    if(get_post_type() != 'eventkrake_location') return $content;
    
    if (!(is_single() || in_the_loop())) {
        return $content; 
    }
    
    try {
        $location = new Location(get_the_ID());
    } catch(\Exception $ex) {
        return $content;
    }
    ob_start(); ?>

    <div class="eventkrake-location">

        <!-- address (without link) -->
        <div class="eventkrake-location-address"><?=
            $location->getAddress()
        ?></div>
        
        <!-- address with link -->
        <div class="eventkrake-location-address-with-link">
            <a href="<?= $location->getAddressUrl() ?>"><?=
                $location->getAddress()
            ?></a>
        </div>
        
        <!-- accessibility info -->
        <div class="eventkrake-accessibility-info"><?=
            $location->getAccessibilityInfo()
        ?></div>
        
        <!-- tags -->
        <div class="eventkrake-location-tags"><?=
            $location->getTags();
        ?></div>
        
        <!-- categories -->
        <div class="eventkrake-event-categories"><?=
            implode(', ', $location->getCategories());
        ?></div>

        <!-- links -->
        <ul class="eventkrake-event-links">
            <?php foreach($location->getLinks() as $link) { ?>
            
                <li><a class="eventkrake-event-link"
                   href="<?= $link['url'] ?>"><?=
                        $link['name']
                ?></a></li>
                            
            <?php } ?>
        </ul>
        
        <!-- events -->
        <div class="eventkrake-location-events"><?php
            
            foreach($location->getEvents() as $event) { ?>
            
                <div class="eventkrake-location-event">
                    
                    <!-- event name & link -->
                    <div class="eventkrake-location-event-title">
                        <a href="<?= $event->getPermalink() ?>"><?=
                            $event->getTitle();
                    ?></a></div>
                    
                    <!-- event excerpt -->
                    <div class="eventkrake-location-event-excerpt"><?=
                        wpautop($event->getExcerpt());
                    ?></div>
                    
                    <!-- event image -->
                    <div class="eventkrake-location-event-image"><?php
                        if (has_post_thumbnail($event->ID)) {
                            print get_the_post_thumbnail($event->ID, 'large');
                        }
                    ?></div>
                </div>
            
            <?php }
        ?></div>

        <!-- content -->
        <div class="eventkrake-location-content"><?=
            $content
        ?></div>
        
    </div><?php
    
    return ob_get_clean();
});

/*
 * add accessibility information to the location post classes 
 */
add_filter('post_class', function($classes, $class, $post_id) {
    if(is_admin()) return $classes;
    
    if (get_post_type() != 'eventkrake_location') {
        return $classes;
    }
    
    $location = new Location($post_id);
    $classes[] = "eventkrake-accessibility-{$location->getAccessibility()}";

    return $classes;
}, 10, 3);