<?php
/*
Plugin Name: Eventkrake
Plugin URI: https://github.com/g4rf/g4rf-eventkrake
Description: A wordpress plugin to manage events, locations and artists. It has an REST endpoint to use the data in external applications.
Author: Jan Kossick
Version: 4.02beta
License: CC BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
Author URI: https://jankossick.de
Min WP Version: 5.3
Text Domain: eventkrake
*/

/***** Needs & needles *****/
add_theme_support('post-thumbnails');

require_once 'Eventkrake/Eventkrake.php';
require_once 'Eventkrake/Event.php';
require_once 'Eventkrake/Artist.php';
require_once 'Eventkrake/Location.php';

use Eventkrake\Eventkrake as Eventkrake;


/***** Scripte & CSS hinzufügen *****/
// Backend JS und CSS
add_action('admin_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

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
        array('jquery', 'eventkrake'));
    wp_enqueue_script('eventkrake_admin');

    // allgemeines CSS
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // Admin-CSS
    wp_register_style('eventkrake_admin', $path.'css/admin.css',
        array('eventkrake_all'));
    wp_enqueue_style('eventkrake_admin');
    // Leaflet CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});

// Frontend JS und CSS
add_action('wp_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // Leaflet-JS
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        array('jquery'));
    wp_enqueue_script('eventkrake_leaflet');
    // allgemeines JS
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        array('eventkrake_leaflet'));
    wp_enqueue_script('eventkrake');

    // allgemeines CSS
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // Leaflet-CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});


/***** Widgets *****/

class Eventkrake_ShowNextEvents extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
        parent::__construct(
			'eventkrake_shownextevents', // id
			esc_html__('Eventkrake :: Next Events', 'eventkrake'), // name
			array( 
                'description' => 
                    esc_html__('Shows the upcoming events.', 'eventkrake')
            ) // args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        $title = $instance['title'];

        $count = $instance['count'];
        if(empty($count)) $count = 0;

        $dateFormatStart = $instance['date_format_start'];
        if(empty($dateFormatStart)) $dateFormatStart = 'c';

        $dateFormatEnd = $instance['date_format_end'];
        if(empty($dateFormatEnd)) $dateFormatEnd = 'c';
        
        
        print $args['before_widget'];
        
        // title
		if(! empty($title)) {
			print $args['before_title'];
            print apply_filters('widget_title', $title);
            print $args['after_title'];
		}
        
        // events
        $data = json_decode( file_get_contents( get_site_url(null, 
            '/wp-json/eventkrake/v3/events?earliestEnd=now',
            is_ssl() ? 'https' : 'http')));
        
        ?><events class="eventkrake"><?php
        foreach($data->events as $event) { ?>
        
            <event class="<?=implode(' ', $event->categories)?> <?=$event->tags ?>">
                <name><?=$event->name ?></name>
                <text><?=$event->text ?></text>
                <picture>
                    <img src="<?=$event->image ?>" alt="<?=$event->name ?>" />
                </picture>
                <start><?=
                    (new DateTime($event->start))->format($dateFormatStart) 
                ?></start>
                <end><?=
                    (new DateTime($event->end))->format($dateFormatEnd) 
                ?></end>
                <categories><?=
                    implode(', ', $event->categories);
                ?></categories>
                <links><?php
                    foreach($event->links as $link) { ?>
                        <a href="<?=$link->url ?>"><?=$link->name ?></a>
                    <?php } ?>
                ?></links>
                <tags><?=$event->tags ?></tags>
                
                <?php // location 
                    $location = $data->locations->{$event->locationid};
                    ?><location class="<?=implode('  ', $location->categories);?>">
                        <name><?=$location->name ?></name>
                        <address><?=$location->address ?></address>
                        <lat><?=$location->lat ?></lat>
                        <lng><?=$location->lng ?></lng>
                        <text><?=$location->text ?></text>
                        <picture>
                            <img src="<?=$location->image?>"
                                 alt="<?=$location->name?>" />
                        </picture>
                        <categories><?=
                            implode(', ', $location->categories);
                        ?></categories>
                        <links><?php
                            foreach($location->links as $link) { ?>
                                <a href="<?=$link->url ?>"><?=$link->name ?></a>
                            <?php } ?>
                        ?></links>
                        <tags><?=$location->tags ?></tags>
                    </location>
                
                <?php // artist
                    foreach($event->artists as $artistId) {
                        $artist = $data->artists->{$artistId};
                        ?><artist class="<?=implode('  ', $artist->categories);?>">
                            <name><?=$artist->name ?></name>
                            <text><?=$artist->text ?></text>
                            <picture>
                                <img src="<?=$artist->image?>"
                                     alt="<?=$artist->name?>" />
                            </picture>
                            <categories><?=
                                implode(', ', $artist->categories);
                            ?></categories>
                            <links><?php
                                foreach($artist->links as $link) { ?>
                                    <a href="<?=$link->url ?>"><?=$link->name ?></a>
                                <?php } ?>
                            ?></links>
                            <tags><?=$artist->tags ?></tags>
                        </artist>
                <?php } ?>                  
            </event>
            
        <?php } ?></events><?php
        
        // after widget
		print $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$title = $instance['title'];

        $count = $instance['count'];
        if(empty($count)) $count = 0;

        $dateFormatStart = $instance['date_format_start'];
        if(empty($dateFormatStart)) $dateFormatStart = 'c';

        $dateFormatEnd = $instance['date_format_end'];
        if(empty($dateFormatEnd)) $dateFormatEnd = 'c';
		?>
        <!-- title -->
		<p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'eventkrake'); ?></label> 
            <input 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_attr($title); ?>">
		</p>
        <!-- count -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>">
                <?php esc_attr_e('Number of events:', 'eventkrake'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('count')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('count')); ?>" 
                type="number" value="<?php echo esc_attr($count); ?>">
		</p>
        <!-- dateFormatStart -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('date_format_start')); ?>">
                <?php esc_attr_e('Date format for start date:', 'eventkrake'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('date_format_start')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('date_format_start')); ?>" 
                type="text" value="<?php echo esc_attr($dateFormatStart); ?>">
		</p>
        <!-- dateFormatEnd -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('date_format_end')); ?>">
                <?php esc_attr_e('Date format for end date:', 'eventkrake'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('date_format_end')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('date_format_end')); ?>" 
                type="text" value="<?php echo esc_attr($dateFormatEnd); ?>">
		</p>
    <?php }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update($new_instance, $old_instance) {
		$instance = array();
        
        // title
		$instance['title'] = '';
        if(! empty($new_instance['title'])) {
            $instance['title'] = sanitize_text_field($new_instance['title']);
        }
        
        // number of events
        $instance['count'] = 0;
        if(! empty($new_instance['count'])) {
            $instance['count'] = sanitize_text_field($new_instance['count']);
        }
        
        // dateFormatStart
        $instance['date_format_start'] = 'c';
        if(! empty($new_instance['date_format_start'])) {
            $instance['date_format_start'] = 
                sanitize_text_field($new_instance['date_format_start']);
        }
        
        // dateFormatEnd
        $instance['date_format_end'] = 'c';
        if(! empty($new_instance['date_format_end'])) {
            $instance['date_format_end'] = 
                sanitize_text_field($new_instance['date_format_end']);
        }

		return $instance;
	}
}
add_action('widgets_init', function() {
    register_widget('Eventkrake_ShowNextEvents');
});

/***** Custom Post Types *****/

/* LOCATIONS */
add_action('init', function () {
    register_post_type('eventkrake_location', array(
        'public' => true,
        'has_archive' => true,
        'taxonomies' => array('category'),
        'labels' => array(
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
        ),
        'rewrite' => array('slug' => 'location'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/location.png',
        'description' =>
            __('An Orten finden Veranstaltungen statt.', 'eventkrake'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_location',
                __('Weitere Angaben zum Ort', 'eventkrake'),
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

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
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
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/event.png',
        'description' => __('Veranstaltungen sind zeitlich begrenzte Ereignisse'
                . ' an einem Ort.', 'eventkrake'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_event',
                __('Weitere Angaben zur Veranstaltung', 'eventkrake'),
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

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
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
        'taxonomies' => array('category'),
        'labels' => array(
            'name' => __('Künstler:innen', 'eventkrake'),
            'singular_name' => __('Künstler:in', 'eventkrake'),
            'add_new' => __('Künstler:in hinzufügen', 'eventkrake'),
            'add_new_item' =>
                    __('Neue Künstler:in hinzufügen', 'eventkrake'),
            'edit' => __('Künstler:in bearbeiten', 'eventkrake'),
            'edit_item' => __('Künstler:in bearbeiten', 'eventkrake'),
            'new_item' => __('Künstler:in hinzufügen', 'eventkrake'),
            'view' => __('Künstler:in ansehen', 'eventkrake'),
            'search_items' => __('Künstler:in suchen', 'eventkrake'),
            'not_found' => __('Keine Künstler:in gefunden', 'eventkrake'),
            'not_found_in_trash' =>
                    __('Keine gelöschten Künstler:innen', 'eventkrake')
        ),
        'rewrite' => array('slug' => 'artist'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/artist.png',
        'description' =>
                __('Künstler:innen sind Einzelpersonen oder
                         Gruppen.', 'eventkrake'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
        'show_in_rest' => true,
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_artist',
                __('Weitere Angaben', 'eventkrake'),
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

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
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
