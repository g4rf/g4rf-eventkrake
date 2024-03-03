<?php

namespace Eventkrake;

class Artist {
    var $ID;
    
    /*
     * static
     */
    
    /**
     * Returns list of all artists.
     * @param array [$filter=[]] @see https://developer.wordpress.org/reference/functions/get_posts/
     * @return array Array of all artists, sorted by title.
     */
    public static function all($filter = []) {
        $options = [
            'numberposts' => -1,
            'offset' => 0,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => 'eventkrake_artist'
        ];
        $posts = get_posts(array_merge($options, $filter));
        
        $artists = [];
        foreach($posts as $post) {
            $artists[] = new Artist($post);
        }
        return $artists;
    }
        
    /*
     * construct
     */
    
    public function __construct($post) {
        $p = get_post($post);
        
        if($p == null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_artist') 
            throw new \Exception('Post type is not `eventkrake_artist`.');
        
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
    
    public function getLinks() {
        return Eventkrake::getSinglePostMeta($this->ID, 'links');
    }
    
    public function getCategories() {
        return Eventkrake::getPostMeta($this->ID, 'categories');
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
                    'key' => 'eventkrake_artists',
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
}

/*
 * add custom post type 
 */
add_action('init', function () {
    register_post_type('eventkrake_artist', [
        'public' => true,
        'has_archive' => true,
        'taxonomies' => ['category'],
        'labels' => [
            'name' => __('Künstler·innen', 'eventkrake'),
            'singular_name' => __('Künstler·in', 'eventkrake'),
            'add_new' => __('Künstler·in hinzufügen', 'eventkrake'),
            'add_new_item' =>
                    __('Neue Künstler·in hinzufügen', 'eventkrake'),
            'edit' => __('Künstler·in bearbeiten', 'eventkrake'),
            'edit_item' => __('Künstler·in bearbeiten', 'eventkrake'),
            'new_item' => __('Künstler·in hinzufügen', 'eventkrake'),
            'view' => __('Künstler·in ansehen', 'eventkrake'),
            'search_items' => __('Künstler·in suchen', 'eventkrake'),
            'not_found' => __('Keine Künstler·in gefunden', 'eventkrake'),
            'not_found_in_trash' =>
                    __('Keine gelöschten Künstler·innen', 'eventkrake')
        ],
        'rewrite' => ['slug' => 'artist'],
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugins_url( '/img/artist.png', dirname(__FILE__) ),
        'description' =>
                __('Künstler·innen sind Einzelpersonen oder Gruppen.', 
                    'eventkrake'),
        'supports' => ['title', 'excerpt', 'editor', 'thumbnail', 
            'comments'],
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // load meta box
            add_meta_box(
                'eventkrake_artist',
                __('Weitere Angaben', 'eventkrake'),
                function($args = null) {
                    // Inhalt der Metabox
                    include dirname(__FILE__) . '/../metabox/artist.php';
                }, null, 'normal', 'high', null
            );
        }
    ]);
});

/*
 * save content of meta box
 */
add_action('save_post_eventkrake_artist', function($post_id, $post) {

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
    
    $artist = new Artist($post_id);

    // tags
    $artist->setTags($properties['eventkrake_tags']);
    
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
    $artist->setLinks($links);

    // categories
    $categories = [];
    if(! empty($properties['eventkrake_categories'])) {
        $cats = explode(",", $properties['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }
    $artist->setCategories($categories);
    
}, 1, 2);

/*
 *  add events to artist page
 */
add_filter('the_content', function($content)
{
    if(is_admin()) return $content;
    if(get_post_type() != 'eventkrake_artist') return $content;
    
    if (!(is_single() || in_the_loop())) {
        return $content; 
    }
    
    try {
        $artist = new Artist(get_the_ID());
    } catch(\Exception $ex) {
        return $content;
    }

    ob_start(); ?>

    <div class="eventkrake-artist">
        
        <!-- tags -->
        <div class="eventkrake-artist-tags"><?=
            $artist->getTags();
        ?></div>
        
        <!-- categories -->
        <div class="eventkrake-artist-categories"><?=
            implode(', ', $artist->getCategories());
        ?></div>

        <!-- links -->
        <ul class="eventkrake-artist-links">
            <?php foreach($artist->getLinks() as $link) { ?>
            
                <li><a class="eventkrake-event-link"
                   href="<?= $link['url'] ?>"><?=
                        $link['name']
                ?></a></li>
                            
            <?php } ?>
        </ul>
        
        <!-- events -->
        <div class="eventkrake-artist-events"><?php
            
            foreach($artist->getEvents() as $event) { ?>
            
                <div class="eventkrake-artist-event">
                    
                    <!-- event name & link -->
                    <div class="eventkrake-artist-event-title">
                        <a href="<?= $event->getPermalink() ?>"><?=
                            $event->getTitle();
                    ?></div>
                    
                    <!-- event excerpt -->
                    <div class="eventkrake-artist-event-excerpt"><?=
                        wpautop($event->getExcerpt());
                    ?></div>
                    
                    <!-- event image -->
                    <div class="eventkrake-artist-event-image"><?php
                        if (has_post_thumbnail($event->ID)) {
                            print get_the_post_thumbnail($event->ID, 'large');
                        }
                    ?></div>
                </div>
            
            <?php }
        ?></div>

        <!-- content -->
        <div class="eventkrake-artist-content"><?=
            $content
        ?></div>
        
    </div><?php
    
    return ob_get_clean();
});